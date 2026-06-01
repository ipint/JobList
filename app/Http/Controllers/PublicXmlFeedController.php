<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\XmlFeed;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Response;

class PublicXmlFeedController extends Controller
{
    public function show(XmlFeed $xmlFeed): Response
    {
        abort_unless($xmlFeed->is_active, 404);

        $companyIds = $xmlFeed->companies()->pluck('companies.id')->all();

        abort_if($companyIds === [], 404);

        $jobs = Job::query()
            ->with('county')
            ->publiclyVisible()
            ->whereIn('company_id', $companyIds)
            ->when(filled($xmlFeed->departments), fn (Builder $query) => $query->whereIn('department', $xmlFeed->departments))
            ->when(filled($xmlFeed->employment_types), fn (Builder $query) => $query->whereIn('employment_type', $xmlFeed->employment_types))
            ->when(filled($xmlFeed->work_modes), fn (Builder $query) => $query->whereIn('work_mode', $xmlFeed->work_modes))
            ->when(filled($xmlFeed->experience_levels), fn (Builder $query) => $query->whereIn('experience_level', $xmlFeed->experience_levels))
            ->orderByDesc('published_at')
            ->get();

        $fields = collect($xmlFeed->selected_fields ?: \App\Models\XmlFeed::defaultXmlFields())
            ->filter(fn ($field): bool => array_key_exists($field, \App\Models\XmlFeed::xmlFieldOptions()))
            ->values()
            ->all();

        if ($fields === []) {
            $fields = \App\Models\XmlFeed::defaultXmlFields();
        }

        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><jobs/>');

        foreach ($jobs as $job) {
            $item = $xml->addChild('job');

            foreach ($fields as $field) {
                if (in_array($field, ['description', 'requirements', 'benefits'], true)) {
                    $descriptionNode = dom_import_simplexml($item->addChild($field));
                    if ($descriptionNode !== false) {
                        $owner = $descriptionNode->ownerDocument;
                        $descriptionNode->appendChild($owner->createCDATASection((string) ($job->{$field} ?? '')));
                    }

                    continue;
                }

                $item->addChild($field, htmlspecialchars($this->resolveFieldValue($job, $field)));
            }
        }

        return response($xml->asXML(), 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
        ]);
    }

    private function resolveFieldValue(Job $job, string $field): string
    {
        $value = $job->{$field} ?? null;

        if (is_bool($value)) {
            return $value ? '1' : '0';
        }

        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d H:i:s');
        }

        return (string) ($value ?? '');
    }
}
