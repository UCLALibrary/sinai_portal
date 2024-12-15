<?php

namespace App\Traits;

use App\Models\Work;
use Illuminate\Support\Facades\DB;

trait RelatedWorkWitnesses
{
    use RelatedWorks;
    
    public function getRelatedWorkWitnesses(string $table, string $id, string $jsonQuery): array
    {
        $workWitnessesQuery = DB::table($table)
            ->selectRaw("jsonb_path_query(jsonb, ?) AS work_witness", [$jsonQuery])
            ->where('id', $id)
            ->get();
        
        return $workWitnessesQuery->map(function ($witness) use ($id) {
            $witnessJsonData = json_decode($witness->work_witness, true);
            
            $witnessData = [
                'work' => $this->processWork($witnessJsonData, $id),
                'locus' => $witnessJsonData['locus'] ?? null,
                'as_written' => $witnessJsonData['as_written'] ?? null,
                'excerpt' => $this->processExcerpts($witnessJsonData['excerpt'] ?? []),
                'note' => $witnessJsonData['note'] ?? null,
                'bib_cites' => $this->processBibliographies($witnessJsonData['bib'] ?? [], 'cite'),
                'bib_editions' => $this->processBibliographies($witnessJsonData['bib'] ?? [], 'edition'),
                'bib_translations' => $this->processBibliographies($witnessJsonData['bib'] ?? [], 'translation'),
            ];
            
            return array_filter($witnessData);
        })->filter()->values()->toArray();
    }
    
    private function processWork(array $witnessJsonData, string $id): ?array
    {
        if (isset($witnessJsonData['work']['id'])) {
            $work = Work::where('ark', $witnessJsonData['work']['id'])->first();
            if ($work) {
                $workJsonData = json_decode($work->jsonb, true);
                
                return [
                    'title' => $workJsonData['pref_title'],
                    'work' => $this->getRelatedWorks('text_units', $id, '$.work_wit[*] ? (@.work.id == "' . $workJsonData['ark'] . '")')[0] ?? null,
                ];
            }
        } elseif (isset($witnessJsonData['work']['desc_title'])) {
            return [
                'title' => $witnessJsonData['work']['desc_title'],
                'genre' => $witnessJsonData['work']['genre'] ?? null,
            ];
        }
        
        return null;
    }
    
    private function processExcerpts(array $excerpts): ?array
    {
        if (empty($excerpts)) {
            return null;
        }
        
        $excerptOrder = ['inc-mut', 'prologue', 'incipit', 'quote', 'explicit', 'des-mut'];
        
        usort($excerpts, function ($a, $b) use ($excerptOrder) {
            return array_search($a['type']['id'], $excerptOrder) <=> array_search($b['type']['id'], $excerptOrder);
        });
        
        return $excerpts;
    }
    
    private function processBibliographies(array $bibliographies, string $type): ?array
    {
        $filtered = array_filter($bibliographies, function ($item) use ($type) {
            return isset($item['type']['id']) && $item['type']['id'] === $type;
        });
        
        return empty($filtered) ? null : $this->getReferencesByJsonObjects($filtered);
    }
}
