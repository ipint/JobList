import { NextResponse } from "next/server";

const ANTHROPIC_MODEL =
  process.env.ANTHROPIC_MODEL || "claude-3-5-sonnet-latest";
const API_BASE_URL = (
  process.env.NEXT_PUBLIC_API_BASE_URL ||
  "http://localhost:8088"
).replace(/\/$/, "");

function extractText(responsePayload) {
  const contentItems = Array.isArray(responsePayload.content)
    ? responsePayload.content
    : [];

  return contentItems
    .map((item) => (item.type === "text" ? item.text || "" : ""))
    .join("")
    .trim();
}

function buildConversationTranscript(messages) {
  return messages
    .map((message) => {
      const role = message.role === "assistant" ? "Assistant" : "User";

      return `${role}: ${String(message.content || "").trim()}`;
    })
    .join("\n");
}

async function getJobsContext() {
  try {
    const response = await fetch(`${API_BASE_URL}/api/jobs?per_page=10`, {
      headers: {
        Accept: "application/json",
      },
      next: { revalidate: 60 },
    });

    if (!response.ok) {
      return "Current jobs context could not be loaded from the backend.";
    }

    const payload = await response.json();
    const jobs = Array.isArray(payload.data) ? payload.data : [];

    if (!jobs.length) {
      return "There are currently no published jobs returned by the backend.";
    }

    return jobs
      .map((job) => {
        const parts = [
          job.title,
          job.company_name,
          job.department,
          job.work_mode,
          job.experience_level,
          job.location?.city,
          job.location?.county,
        ].filter(Boolean);

        return `- ${parts.join(" | ")} | slug: ${job.slug}`;
      })
      .join("\n");
  } catch {
    return "Current jobs context could not be loaded from the backend.";
  }
}

export async function POST(request) {
  if (!process.env.ANTHROPIC_API_KEY) {
    return NextResponse.json(
      {
        error: "ANTHROPIC_API_KEY is missing. Add it to frontend/.env.local.",
      },
      { status: 500 }
    );
  }

  try {
    const body = await request.json();
    const messages = Array.isArray(body.messages) ? body.messages.slice(-8) : [];

    if (!messages.length) {
      return NextResponse.json({ error: "No messages were provided." }, { status: 400 });
    }

    const jobsContext = await getJobsContext();
    const conversationTranscript = buildConversationTranscript(messages);

    const response = await fetch("https://api.anthropic.com/v1/messages", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "x-api-key": process.env.ANTHROPIC_API_KEY,
        "anthropic-version": "2023-06-01",
      },
      body: JSON.stringify({
        model: ANTHROPIC_MODEL,
        system:
          "You are a concise jobs assistant for a UK jobs board built on Laravel and Next.js. Use the provided jobs context when it is relevant. If the user asks about current roles, mention matching job titles and slugs when possible. If you are unsure, say so plainly.",
        max_tokens: 350,
        messages: [
          {
            role: "user",
            content: `Current jobs context:\n${jobsContext}\n\nConversation so far:\n${conversationTranscript}`,
          },
        ],
      }),
    });

    const payload = await response.json();

    if (!response.ok) {
      return NextResponse.json(
        {
          error: payload.error?.message || "Anthropic returned an error.",
        },
        { status: response.status }
      );
    }

    const message =
      extractText(payload) || "I could not generate a response from the current chat request.";

    return NextResponse.json({ message });
  } catch {
    return NextResponse.json(
      {
        error: "The chat request failed before a response was returned.",
      },
      { status: 500 }
    );
  }
}
