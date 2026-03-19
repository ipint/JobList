import Link from "next/link";
import { notFound } from "next/navigation";
import { getJobBySlug } from "@/lib/jobs-api";

function formatValue(value, fallback = "Not specified") {
  return value || fallback;
}

function formatSalary(salary) {
  if (!salary?.is_visible) {
    return "Salary not disclosed";
  }

  if (salary.text) {
    return salary.text;
  }

  if (salary.min || salary.max) {
    const min = salary.min ? `GBP ${Number(salary.min).toLocaleString("en-GB")}` : null;
    const max = salary.max ? `GBP ${Number(salary.max).toLocaleString("en-GB")}` : null;

    return [min, max].filter(Boolean).join(" - ");
  }

  return "Discussed during the process";
}

function DetailSection({ title, body }) {
  if (!body) {
    return null;
  }

  return (
    <section className="job-panel detail-section">
      <h2>{title}</h2>
      <div className="detail-copy">
        <p>{body}</p>
      </div>
    </section>
  );
}

export async function generateMetadata({ params }) {
  const job = await getJobBySlug(params.slug);

  if (!job) {
    return {
      title: "Job not found | JobList",
    };
  }

  return {
    title: `${job.title} | JobList`,
    description: `${job.company_name} in ${job.location.city || job.location.county || "the UK"}.`,
  };
}

export default async function JobAdvertPage({ params }) {
  const job = await getJobBySlug(params.slug);

  if (!job) {
    notFound();
  }

  return (
    <>
      <Link href="/jobs" className="back-link">
        Back to jobs
      </Link>

      <section className="job-panel job-header">
        <div className="job-tags">
          {job.is_featured ? <span className="pill featured">Featured</span> : null}
          <span className="pill">{formatValue(job.department)}</span>
          <span className="pill">{formatValue(job.work_mode)?.replace("_", " ")}</span>
          <span className="pill">{formatValue(job.employment_type)?.replace("_", " ")}</span>
        </div>
        <h1>{job.title}</h1>
        <p>{job.company_name}</p>
        <div className="detail-grid">
          <span className="pill">{formatValue(job.location.location_name, job.location.city)}</span>
          {job.location.county ? <span className="pill">{job.location.county}</span> : null}
          <span className="pill">{formatSalary(job.salary)}</span>
          <span className="pill">Closing: {formatValue(job.closing_date)}</span>
        </div>
      </section>

      <section className="job-layout">
        <div className="detail-stack">
          <DetailSection title="Role overview" body={job.description} />
          <DetailSection title="Requirements" body={job.requirements} />
          <DetailSection title="Benefits" body={job.benefits} />
        </div>

        <aside className="detail-stack">
          <section className="job-panel detail-section">
            <h2>At a glance</h2>
            <dl className="detail-list">
              <div className="detail-item">
                <dt>Reference</dt>
                <dd>{formatValue(job.reference)}</dd>
              </div>
              <div className="detail-item">
                <dt>Experience</dt>
                <dd>{formatValue(job.experience_level)}</dd>
              </div>
              <div className="detail-item">
                <dt>City</dt>
                <dd>{formatValue(job.location.city)}</dd>
              </div>
              <div className="detail-item">
                <dt>Postcode</dt>
                <dd>{formatValue(job.location.postcode)}</dd>
              </div>
              <div className="detail-item">
                <dt>Visa sponsorship</dt>
                <dd>{job.visa_sponsorship_available ? "Available" : "No"}</dd>
              </div>
              <div className="detail-item">
                <dt>Right to work required</dt>
                <dd>{job.right_to_work_required ? "Yes" : "No"}</dd>
              </div>
            </dl>
          </section>

          <section className="job-panel detail-section">
            <h2>Apply</h2>
            <dl className="detail-list">
              <div className="detail-item">
                <dt>Application URL</dt>
                <dd>
                  {job.application.url ? (
                    <a href={job.application.url} target="_blank" rel="noreferrer">
                      {job.application.url}
                    </a>
                  ) : (
                    "Not provided"
                  )}
                </dd>
              </div>
              <div className="detail-item">
                <dt>Application email</dt>
                <dd>{formatValue(job.application.email)}</dd>
              </div>
            </dl>
          </section>
        </aside>
      </section>
    </>
  );
}
