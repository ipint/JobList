"use client";

import { useEffect, useRef, useState } from "react";

const STARTER_MESSAGE = {
  role: "assistant",
  content: "Ask about jobs, filters, or the vacancies currently listed here.",
};

export default function ChatbotWidget() {
  const [isOpen, setIsOpen] = useState(false);
  const [messages, setMessages] = useState([STARTER_MESSAGE]);
  const [input, setInput] = useState("");
  const [isLoading, setIsLoading] = useState(false);
  const [error, setError] = useState("");
  const scrollerRef = useRef(null);

  useEffect(() => {
    if (scrollerRef.current) {
      scrollerRef.current.scrollTop = scrollerRef.current.scrollHeight;
    }
  }, [messages, isOpen]);

  async function handleSubmit(event) {
    event.preventDefault();

    const trimmed = input.trim();

    if (!trimmed || isLoading) {
      return;
    }

    const nextMessages = [...messages, { role: "user", content: trimmed }];

    setMessages(nextMessages);
    setInput("");
    setError("");
    setIsLoading(true);

    try {
      const response = await fetch("/api/chat", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({
          messages: nextMessages,
        }),
      });

      const payload = await response.json();

      if (!response.ok) {
        throw new Error(payload.error || "Chat request failed.");
      }

      setMessages((currentMessages) => [
        ...currentMessages,
        {
          role: "assistant",
          content: payload.message,
        },
      ]);
    } catch (requestError) {
      setError(requestError.message || "Something went wrong.");
    } finally {
      setIsLoading(false);
    }
  }

  return (
    <div className="chatbot-shell">
      {isOpen ? (
        <section id="jobs-chatbot" className="chatbot-panel" aria-label="Jobs assistant">
          <div className="chatbot-header">
            <div>
              <p className="chatbot-eyebrow">Assistant</p>
              <h2>Jobs Chat</h2>
            </div>
            <button
              type="button"
              className="chatbot-toggle chatbot-close"
              onClick={() => setIsOpen(false)}
              aria-label="Close chatbot"
            >
              x
            </button>
          </div>

          <div className="chatbot-messages" ref={scrollerRef}>
            {messages.map((message, index) => (
              <div
                key={`${message.role}-${index}`}
                className={
                  message.role === "assistant"
                    ? "chatbot-message chatbot-message-assistant"
                    : "chatbot-message chatbot-message-user"
                }
              >
                {message.content}
              </div>
            ))}
            {isLoading ? (
              <div className="chatbot-message chatbot-message-assistant">Thinking...</div>
            ) : null}
          </div>

          {error ? <p className="chatbot-error">{error}</p> : null}

          <form className="chatbot-form" onSubmit={handleSubmit}>
            <label className="sr-only" htmlFor="chatbot-message">
              Your message
            </label>
            <textarea
              id="chatbot-message"
              name="message"
              rows={3}
              value={input}
              onChange={(event) => setInput(event.target.value)}
              placeholder="Ask about roles, locations, or current openings..."
            />
            <button type="submit" className="apply-link button-reset" disabled={isLoading}>
              Send
            </button>
          </form>
        </section>
      ) : null}

      <button
        type="button"
        className="chatbot-toggle chatbot-launch"
        onClick={() => setIsOpen((currentValue) => !currentValue)}
        aria-expanded={isOpen}
        aria-controls="jobs-chatbot"
      >
        Chat
      </button>
    </div>
  );
}
