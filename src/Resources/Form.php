<?php

namespace Day4\NovaForms\Resources;

use Day4\Nova\TranslatableResource;
use Day4\SwitchLocale\Language;
use Drobee\NovaSluggable\Slug;
use Drobee\NovaSluggable\SluggableText;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\File;
use Laravel\Nova\Fields\Heading;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\KeyValue;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Resource;
use Whitecube\NovaFlexibleContent\Flexible;

class Form extends TranslatableResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static string $model = \Day4\NovaForms\Models\Form::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static string $title = 'title';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static array $search = [
        'title',
    ];

    public static string $group = 'Forms';

    /**
     * Get the fields displayed by the resource.
     *
     * @param Request $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            ID::make()->sortable(),
            Language::make(__('Translation')),
            Text::make(__('Label'), 'label')->sortable()->required(true),
            SluggableText::make(__('Title'), 'title')
                ->slug(__('Slug'))
                ->sortable()
                ->required(true),
            Slug::make(__('Slug'), 'slug')
                ->slugUnique()
                ->slugModel('\App\PageTranslation')
                ->onlyOnForms()
                ->slugLanguage(app()->getLocale()),
            Textarea::make(__('Excerpt'), 'excerpt'),
            Boolean::make(__('Active'), 'is_active')->sortable(),

            Heading::make('Notification Settings'),
            Text::make(__('Email'), 'email'),

            Heading::make('Form Properties'),
            Text::make(__('Button label'), 'btn')->default('Send')->required(true),
            Text::make(__('Sent message'), 'msg')->default('Thank you')->required(true),

            Flexible::make(__('Fields'), 'fields')
                ->addLayout('Text field', 'text', [
                    Text::make(__('Label'), 'lbl'),
                    Text::make(__('Placeholder'), 'ph'),
                    Boolean::make(__('Required'), 'req'),
                    Number::make(__('Min length'), 'min')->min(0)->max(190)->step(1),
                    Number::make(__('Max length'), 'max')->min(1)->max(190)->step(1)
                ])
                ->addLayout('Email field', 'email', [
                    Text::make(__('Label'), 'lbl'),
                    Text::make(__('Placeholder'), 'ph'),
                    Boolean::make(__('Required'), 'req')
                ])
                ->addLayout('Phone field', 'tel', [
                    Text::make(__('Label'), 'lbl'),
                    Text::make(__('Placeholder'), 'ph'),
                    Boolean::make(__('Required'), 'req')
                ])
                ->addLayout('Textarea field', 'textarea', [
                    Text::make(__('Label'), 'lbl'),
                    Text::make(__('Placeholder'), 'ph'),
                    Boolean::make(__('Required'), 'req'),
                    Number::make(__('Min length'), 'min')->min(0)->max(300)->step(1),
                    Number::make(__('Max length'), 'max')->min(1)->max(300)->step(1)
                ])
                ->addLayout('Question field', 'question', [
                    Text::make(__('Label'), 'lbl'),
                    KeyValue::make(__('Options'), 'options')->rules('json'),
                    Boolean::make(__('Allow multiple'), 'multi'),
                    Boolean::make(__('Required'), 'req'),
                ])->button(__('Add Field'))->stacked(),
            Flexible::make(__('Terms'), 'terms')
                ->addLayout('Downloadable T&Cs ', 'file', [
                    Text::make(__('Label'), 'lbl'),
                    Boolean::make(__('Required'), 'req'),
                    Text::make(__('Text'), 'text'),
                    File::make(__('File'), 'file')
                        ->disk('public')
                        ->path('docs')
                ])
                ->addLayout('Linkable T&Cs', 'link', [
                    Text::make(__('Label'), 'lbl'),
                    Boolean::make(__('Required'), 'req'),
                    Text::make(__('Text'), 'text'),
                    Text::make(__('Url'), 'url')
                ])->button('Add T&Cs')->stacked(),
        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @param Request $request
     * @return array
     */
    public function cards(Request $request)
    {
        return [
            new \Day4\NovaForms\Metrics\Entries,
        ];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param Request $request
     * @return array
     */
    public function filters(Request $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param Request $request
     * @return array
     */
    public function lenses(Request $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param Request $request
     * @return array
     */
    public function actions(Request $request)
    {
        return [];
    }
}
