<?php

namespace Castle\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmptyTrash extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'trash:flush {--c|class= : Flush a specific class}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Permanently delete soft-deleted objects';

    /**
     * List of classes to check for deleted entries.
     *
     * @var array
     */
    protected $classes = [
        \Castle\Client::class,
        \Castle\Comment::class,
        \Castle\Discussion::class,
        \Castle\DiscussionStatus::class,
        \Castle\Document::class,
        \Castle\Permission::class,
        \Castle\Resource::class,
        \Castle\ResourceType::class,
        \Castle\Tag::class,
        \Castle\User::class,
        \Castle\Vote::class,
    ];

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $classes = (array) $this->option('class') ?: $this->classes;
        $sum = 0;

        foreach ($classes as $class) {
            if (in_array(SoftDeletes::class, class_uses($class))) {
                $count = $this->flush($class);

                if ($count > 0) {
                    $this->info('Permanently deleted '.$count.' '.class_basename($class).'.');
                }

                $sum += $count;
            } else if ($this->option('class')) {
                $this->comment(class_basename($class).' does not use soft-deletes.');
            }
        }

        $this->info($sum . ' trashed records deleted.');
    }

    /**
     * Flushes soft-deleted records for a specific class.
     *
     * @param $class string Class name
     * @param $filter Closure Optional exclusion filter callback
     *   (records returning true will not be deleted)
     * @return int Number of records deleted
     */
    protected function flush($class, $filter = null)
    {
        $all = $class::onlyTrashed()->get();
        $sum = 0;

        if ($filter) {
            $all = $all->filter($filter);
        }

        $all->each(function($object) use ($sum) {
            $object->forceDelete();
            $sum ++;
        });

        return $sum;
    }
}
