<?php

namespace Day4\NovaForms\GraphQL\Mutations;

use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Request;
use GrahamCampbell\Throttle\Facades\Throttle;
use Day4\NovaForms\Notifications\NewFormEntry;
use Day4\NovaForms\Models\Form;
use Day4\NovaForms\Models\FormEntry;

class FormSubmit
{
    /**
     * Return a value for the field.
     *
     * @param  null  $rootValue Usually contains the result returned from the parent field. In this case, it is always `null`.
     * @param  mixed[]  $args The arguments that were passed into the field.
     * @param GraphQLContext $context Arbitrary data that is shared between all fields of a single query.
     * @param ResolveInfo $resolveInfo Information about the query itself, such as the execution state, the field name, path to the field from the root, and more.
     * @return mixed
     */
    public function __invoke($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $request = Request::instance();

        if (!Throttle::attempt($request)) {
            return [
                'success' => false,
                'msg' => 'Rate limit exceeded'
            ];
        }

        $formId = $args['formId'];
        $values = [];
        foreach ($args['fields'] as $F) {
            $values[$F['n']] = $F['v'];
        }

        $locale = app()->getLocale();
        /* @var Form $form */
        $form = Form::query()->find($formId);
        if (! $form || ! $form->hasTranslation($locale)) {
            return [
                'success' => false,
                'msg' => 'Form not found for this language'
            ];
        }
        $fieldDefs = json_decode($form->fields ?? '[]');
        $termDefs = json_decode($form->terms ?? '[]');

        $fields = [];
        $terms = [];

        try {
            foreach ($fieldDefs as $F) {
                if ($values[$F->key] ?? false) {
                    if ($F->attributes->min ?? false) {
                        if ($F->layout == 'number') {
                            if ($values[$F->key] < $F->attributes->min) {
                                throw new \Exception($F->attributes->lbl . ": needs to be greater than " . $F->attributes->min);
                            }
                        } else if (strlen($values[$F->key]) < $F->attributes->min) {
                            throw new \Exception($F->attributes->lbl . ": needs to be longer than " . $F->attributes->min . " characters");
                        }
                    }
                    if ($F->attributes->max ?? false) {
                        if ($F->layout == 'number') {
                            if ($values[$F->key] > $F->attributes->max) {
                                throw new \Exception($F->attributes->lbl . ": needs to be less than " . $F->attributes->max);
                            }
                        } else if (strlen($values[$F->key]) > $F->attributes->max) {
                            throw new \Exception($F->attributes->lbl . ": needs to be shorter than " . $F->attributes->max . " characters");
                        }
                    }

                    if ($F->layout == 'number') {
                        $v = (int) $values[$F->key];
                    } else $v = filter_var($values[$F->key], FILTER_SANITIZE_STRING);

                    $fields[$F->attributes->lbl] = $v;
                } else if ($F->attributes->req ?? false) {
                    throw new \Exception($F->attributes->lbl . ": is required");
                }
            }
            foreach ($termDefs as $T) {
                $b = (bool) ($values[$T->key] ?? false);
                if ($T->attributes->req ?? false && !$b) {
                    throw new \Exception($T->attributes->lbl . ": is required");
                } else {
                    $terms[$T->attributes->lbl] = $b;
                }
            }
        } catch (\Throwable $err) {
            return [
                'success' => false,
                'errors' => [$err->getMessage()]
            ];
        }

        $entry = FormEntry::create([
            'form_id' => $formId,
            'locale' => $locale,
            'fields' => $fields,
            'terms' => $terms,
        ]);

        if ($form->email ?? false) {
            Notification::route('mail', $form->email)
                ->notify(new NewFormEntry($entry));
        }

        return [
            'success' => true,
            'msg' => $form->msg
        ];
    }
}
