<?php

namespace Fliva\LaravelHasManySync;

use App\Models\Member;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Concerns\InteractsWithPivotTable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Arr;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    use InteractsWithPivotTable {
        formatRecordsList as public;
        parseIds as public;
        castKeys as public;
    }

    public Model $related;

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $that = $this;

        HasMany::macro('sync', function (array $ids, $deleting = true, bool $syncRelateKey = true) use ($that) {
            /** @var HasMany $this */

            $changes = [
                'created' => [], 'deleted' => [], 'updated' => [],
            ];

            // Needed due \Illuminate\Database\Eloquent\Relations\Concerns\InteractsWithPivotTable::castKey
            $that->related = $this->getRelated();

            // Get the keys of the relationship
            $relatedKeyName = $that->related->getKeyName();
            $foreignKeyName = $this->getForeignKeyName();
            $parentKey = $this->getParentKey();

            // First we need to attach any of the associated models that are not currently
            // in this joining table. We'll spin through the given IDs, checking to see
            // if they exist in the array of current ones, and if not we will insert.
            $current = $this->newQuery()->pluck($relatedKeyName)->all();

            $records = $that->formatRecordsList($that->parseIds($ids));

            // Next, we will take the differences of the currents and given IDs and detach
            // all of the entities that exist in the "current" array but are not in the
            // array of the new IDs given to the method which will complete the sync.
            if ($deleting) {
                $delete = array_diff($current, array_keys($records));

                if (count($delete) > 0) {
                    $that->related->destroy($delete);

                    $changes['deleted'] = $that->castKeys($delete);
                }
            }

            // Get the records to update
            $update = Arr::only($records, $current);
            $changes['updated'] = $that->castKeys(array_keys($update));

            // Get the records to create
            $create = Arr::except($records, array_keys($update));
            $changes['created'] = $that->castKeys(array_keys($create));

            // Build the set of records to create or update in batch
            $records = Arr::map(array_merge($create, $update),
                fn($attributes, $key) => array_merge($attributes, [
                    $relatedKeyName => $key,
                    $foreignKeyName => $parentKey,
                ])
            );

            // Avoids synchronization of the key of the related model.
            if (! $syncRelateKey) {
                $records = array_map(fn($record) => Arr::except($record, $relatedKeyName), $records);
            }

            // Do the insert or update batch
            $that->related->upsert($records, $relatedKeyName);

            return $changes;
        });
    }
}
