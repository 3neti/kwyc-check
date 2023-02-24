<?php

namespace App\Nova;

use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use App\Enums\ChannelEnum;
use App\Enums\FormatEnum;

class Repository extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\Repository>
     */
    public static $model = \App\Models\Repository::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'name';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id',
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [
            Text::make('Name')
                ->sortable()
                ->rules('required', 'max:200')
                ->creationRules('unique:repositories,name')
                ->updateRules('unique:repositories,name,{{resourceId}}')
                ->required(),

            Select::make('Channel')
                ->options(ChannelEnum::novaOptions()),

            Select::make('Format')
                ->options(FormatEnum::novaOptions()),

            Text::make('Address')
                ->sortable()
                ->rules('required', 'max:200')
                ->required(),

            Textarea::make('command')
                ->rules('required', 'max:200')
                ->required(),

            BelongsTo::make('Organization')
                ->display( function($model) {
                    return $model->name;
                }),
        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function cards(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function filters(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function lenses(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function actions(NovaRequest $request)
    {
        return [];
    }

    public static function indexQuery(NovaRequest $request, $query)
    {
        return $query->whereHas('organization', function ($q) use ($request) {
            $q->whereHas('admin', function ($q) use ($request) {
                $q->where('admin_id', $request->user()->id);
            });
        });
    }
}
