import "./globals.css";
import Link from "next/link";
import ChatbotWidget from "@/components/chatbot-widget";

export const metadata = {
  title: "JobList Frontend",
  description: "Public jobs frontend for the JobList Laravel API.",
};

export default function RootLayout({ children }) {
  return (
    <html lang="en">
      <body>
        <div className="shell">
          <header className="site-header">
            <Link href="/jobs" className="brand">
              <span className="brand-mark">JobList</span> Public Jobs
            </Link>
            <Link href="/jobs" className="nav-link">
              Browse jobs
            </Link>
          </header>
          {children}
        </div>
        <ChatbotWidget />
      </body>
    </html>
  );
}
