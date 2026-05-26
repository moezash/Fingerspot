import Link from "next/link";
import { Construction } from "lucide-react";

import { Button } from "@/components/ui/button";
import {
  ContentSection,
  EmptyState,
  PageHeader,
} from "@/components/shared";

type SectionPlaceholderPageProps = {
  params: Promise<{ slug: string[] }>;
};

function formatSectionTitle(slug: string[]) {
  return slug
    .join(" ")
    .replace(/-/g, " ")
    .replace(/\b\w/g, (char) => char.toUpperCase());
}

export default async function SectionPlaceholderPage({
  params,
}: SectionPlaceholderPageProps) {
  const { slug } = await params;
  const title = formatSectionTitle(slug);

  return (
    <div className="flex flex-col gap-10">
      <PageHeader
        label="Module"
        title={title}
        description="This module is being prepared for API integration. Use the patterns below when building out this page."
      />

      <ContentSection>
        <EmptyState
          icon={Construction}
          title={`${title} is not ready yet`}
          description="Tables, filters, and API-connected views will be added in a future phase. The shared UI components below are ready to use."
        >
          <Button variant="outline" size="sm" className="h-8" asChild>
            <Link href="/dashboard">Back to dashboard</Link>
          </Button>
        </EmptyState>
      </ContentSection>
    </div>
  );
}
