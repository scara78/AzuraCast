<?php
namespace App\Sync\Task;

use App\Entity;
use Cake\Chronos\Chronos;

class HistoryCleanup extends AbstractTask
{
    public function run($force = false): void
    {
        $days_to_keep = (int)$this->settingsRepo->getSetting(Entity\Settings::HISTORY_KEEP_DAYS,
            Entity\SongHistory::DEFAULT_DAYS_TO_KEEP);

        if ($days_to_keep !== 0) {
            $threshold = (new Chronos())
                ->subDays($days_to_keep)
                ->getTimestamp();

            $this->em->createQuery(/** @lang DQL */ 'DELETE 
                FROM App\Entity\SongHistory sh 
                WHERE sh.timestamp_start != 0 
                AND sh.timestamp_start <= :threshold')
                ->setParameter('threshold', $threshold)
                ->execute();
        }

        // Clean up any history entries that don't represent actual played songs.
        $cleanup_threshold = time() - 43200;

        $this->em->createQuery(/** @lang DQL */ 'DELETE 
            FROM App\Entity\SongHistory sh
            WHERE sh.timestamp_cued < :threshold
            AND sh.timestamp_start = 0
            AND sh.timestamp_end = 0')
            ->setParameter('threshold', $cleanup_threshold)
            ->execute();
    }
}
