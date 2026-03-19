import Link from "next/link";
import { getJobs } from "@/lib/jobs-api";

const FILTER_FIELDS = [
  ["county", "County"],
  ["department", "Department"],
  ["employment_type", "Employment type"],
  ["work_mode", "Work mode"],
  ["experience_level", "Experience level"],
];

function formatOptionLabel(value) {
  return value.replaceAll("_", " ").replace(/\b\w/g, (character) => character.toUpperCase());
}

function buildFilterOptions(jobs) {
  return {
    county: [...new Set(jobs.map((job) => job.location?.county_slug).filter(Boolean))].sort(),
    department: [...new Set(jobs.map((job) => job.department).filter(Boolean))].sort(),
    employment_type: [...new Set(jobs.map((job) => job.employment_type).filter(Boolean))].sort(),
    work_mode: [...new Set(jobs.map((job) => job.work_mode).filter(Boolean))].sort(),
    experience_level: [...new Set(jobs.map((job) => job.experience_level).filter(Boolean))].sort(),
  };
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

  return "Salary details on application";
}

function buildPageHref(filters, page) {
  const searchParams = new URLSearchParams();

  Object.entries(filters).forEach(([key, value]) => {
    if (value) {
      searchParams.set(key, value);
    }
  });

  searchParams.set("page", String(page));

  return `/jobs?${searchParams.toString()}`;
}

function Pagination({ filters, meta }) {
  if (!meta?.last_page || meta.last_page <= 1) {
    return null;
  }

  const currentPage = meta.current_page ?? 1;
  const totalPages = meta.last_page ?? 1;
  const pageNumbers = [];

  for (let page = 1; page <= totalPages; page += 1) {
    pageNumbers.push(page);
  }

  return (
    <nav className="pagination" aria-label="Jobs pagination">
      <div className="pagination-summary">
        Showing {meta.from ?? 0}-{meta.to ?? 0} of {meta.total ?? 0} jobs
      </div>
      <div className="pagination-links">
        {currentPage > 1 ? (
          <Link href={buildPageHref(filters, currentPage - 1)} className="nav-link">
            Previous
          </Link>
        ) : null}

        {pageNumbers.map((page) => (
          <Link
            key={page}
            href={buildPageHref(filters, page)}
            className={page === currentPage ? "pagination-link pagination-link-active" : "pagination-link"}
          >
            {page}
          </Link>
        ))}

        {currentPage < totalPages ? (
          <Link href={buildPageHref(filters, currentPage + 1)} className="nav-link">
            Next
          </Link>
        ) : null}
      </div>
    </nav>
  );
}

function SearchAndFilters({ filters, options }) {
  return (
    <section className="listing-shell">
      <aside className="job-panel filters-panel">
        <form className="filters-form" action="/jobs" method="get">
          <div className="field-group field-group-search">
            <label htmlFor="search">Keyword search</label>
            <input
              id="search"
              name="search"
              type="search"
              defaultValue={filters.search}
              placeholder="Laravel, designer, operations..."
            />
          </div>

          {FILTER_FIELDS.map(([key, label]) => (
            <div className="field-group" key={key}>
              <label htmlFor={key}>{label}</label>
              <select id={key} name={key} defaultValue={filters[key]}>
                <option value="">All</option>
                {options[key].map((value) => (
                  <option value={value} key={value}>
                    {formatOptionLabel(value)}
                  </option>
                ))}
              </select>
            </div>
          ))}

          <div className="filter-actions">
            <button type="submit" className="apply-link button-reset">
              Apply filters
            </button>
            <Link href="/jobs" className="nav-link">
              Reset
            </Link>
          </div>
        </form>
      </aside>
    </section>
  );
}

function JobCard({ job }) {
  return (
    <article className="job-card">
      <div className="detail-stack">
        <div className="job-tags">
          {job.is_featured ? <span className="pill featured">Featured</span> : null}
          <span className="pill">{job.department}</span>
          <span className="pill">{job.work_mode.replace("_", " ")}</span>
        </div>

        <div className="detail-stack">
          <h2>{job.title}</h2>
          <p className="company">{job.company_name}</p>
        </div>

        <div className="job-meta">
          <span className="pill">{job.location.city}</span>
          {job.location.county ? <span className="pill">{job.location.county}</span> : null}
          {job.employment_type ? <span className="pill">{job.employment_type.replace("_", " ")}</span> : null}
          {job.experience_level ? <span className="pill">{job.experience_level}</span> : null}
        </div>
      </div>

      <div className="card-footer">
        <span>{formatSalary(job.salary)}</span>
        <Link href={`/jobs/${job.slug}`} className="apply-link">
          View advert
        </Link>
      </div>
    </article>
  );
}

export const metadata = {
  title: "Jobs | JobList",
  description: "Browse current public jobs from the JobList backend.",
};

function normalizeFilters(searchParams = {}) {
  return {
    search: searchParams.search ?? "",
    county: searchParams.county ?? "",
    department: searchParams.department ?? "",
    employment_type: searchParams.employment_type ?? "",
    work_mode: searchParams.work_mode ?? "",
    experience_level: searchParams.experience_level ?? "",
    page: searchParams.page ?? "1",
  };
}

export default async function JobsPage({ searchParams }) {
  const filters = normalizeFilters(searchParams);
  const [{ data: jobs, meta }, { data: allJobs }] = await Promise.all([
    getJobs({ ...filters, per_page: 10 }),
    getJobs({ per_page: 50 }),
  ]);
  const filterOptions = buildFilterOptions(allJobs);

  return (
    <>
      <section className="hero">
        <p className="eyebrow">Live recruitment</p>
        <h1>Current openings from the Laravel jobs API.</h1>
        <p>
          This Next.js frontend reads the published jobs feed and renders each vacancy as a
          public advert page.
        </p>
        <div className="hero-meta">
          <span className="pill">{meta.total ?? jobs.length} live jobs loaded</span>
          <span className="pill">Card listing</span>
          <span className="pill">Slug-based advert pages</span>
        </div>
      </section>

      <div className="keyword-bar job-panel">
        <form action="/jobs" method="get" className="keyword-form">
          <div className="field-group">
            <label htmlFor="keyword-search">Search jobs</label>
            <input
              id="keyword-search"
              name="search"
              type="search"
              defaultValue={filters.search}
              placeholder="Try: remote Laravel jobs"
            />
          </div>

          {Object.entries(filters)
            .filter(([key, value]) => key !== "search" && key !== "page" && value)
            .map(([key, value]) => (
              <input key={key} type="hidden" name={key} value={value} />
            ))}

          <button type="submit" className="apply-link button-reset">
            Search
          </button>
        </form>
      </div>

      <section className="listing-layout">
        <SearchAndFilters filters={filters} options={filterOptions} />
        <div className="results-stack">
          {(filters.search ||
            filters.county ||
            filters.department ||
            filters.employment_type ||
            filters.work_mode ||
            filters.experience_level) && (
            <div className="active-filters">
              {filters.search ? <span className="pill">Keyword: {filters.search}</span> : null}
              {filters.county ? <span className="pill">County: {formatOptionLabel(filters.county)}</span> : null}
              {filters.department ? <span className="pill">Department: {filters.department}</span> : null}
              {filters.employment_type ? <span className="pill">Type: {formatOptionLabel(filters.employment_type)}</span> : null}
              {filters.work_mode ? <span className="pill">Mode: {formatOptionLabel(filters.work_mode)}</span> : null}
              {filters.experience_level ? <span className="pill">Level: {formatOptionLabel(filters.experience_level)}</span> : null}
            </div>
          )}

          {jobs.length ? (
            <>
            <section className="jobs-grid">
              {jobs.map((job) => (
                <JobCard key={job.id} job={job} />
              ))}
            </section>

            <Pagination filters={filters} meta={meta} />
            </>
      ) : (
            <section className="empty-state">
              <h2>No jobs matched those filters.</h2>
              <p>Try a broader keyword or clear one of the left-side filters.</p>
            </section>
      )}
        </div>
      </section>
    </>
  );
}
