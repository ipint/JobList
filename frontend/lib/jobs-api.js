const API_BASE_URL = (
  process.env.NEXT_PUBLIC_API_BASE_URL ||
  "http://localhost:8088"
).replace(/\/$/, "");

async function getJson(path) {
  const response = await fetch(`${API_BASE_URL}${path}`, {
    next: { revalidate: 60 },
    headers: {
      Accept: "application/json",
    },
  });

  if (response.status === 404) {
    return null;
  }

  if (!response.ok) {
    throw new Error(`Jobs API request failed: ${response.status}`);
  }

  return response.json();
}

function toQueryString(params = {}) {
  const searchParams = new URLSearchParams();

  Object.entries(params).forEach(([key, value]) => {
    if (value === undefined || value === null || value === "") {
      return;
    }

    searchParams.set(key, String(value));
  });

  const queryString = searchParams.toString();

  return queryString ? `?${queryString}` : "";
}

export async function getJobs(params = {}) {
  const payload = await getJson(`/api/jobs${toQueryString(params)}`);

  return {
    data: payload?.data ?? [],
    links: payload?.links ?? {},
    meta: payload?.meta ?? {},
  };
}

export async function getJobBySlug(slug) {
  const payload = await getJson(`/api/jobs/${slug}`);

  return payload?.data ?? null;
}
