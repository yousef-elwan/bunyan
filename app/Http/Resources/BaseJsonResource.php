<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BaseJsonResource extends JsonResource
{
    protected ?string $selectedFields = null;

    public function withFields(?string $fields): static
    {
        $this->selectedFields = $fields;
        return $this;
    }

    public function toArray($request)
    {

        // الخطوة 1: اجلب البيانات من الدالة المخصصة (من child)
        $data = method_exists($this, 'resolveData')
            ? $this->resolveData($request)
            : \Illuminate\Http\Resources\Json\JsonResource::toArray($request);


        // الخطوة 2: إذا لم يتم تمرير fields → أرجع كل شيء
        if (is_null($this->selectedFields) || trim($this->selectedFields) === '') {
            return $data;
        }

        // الخطوة 3: فلترة الحقول حسب fields
        return $this->filterFields($data, $this->selectedFields);
    }

    protected function filterFields(array $data, string $fieldsString): array
    {
        $fields = explode(',', $fieldsString);
        $result = [];

        foreach ($fields as $field) {
            $parts = explode('.', $field);
            $ref = &$result;
            $source = $data;

            foreach ($parts as $i => $part) {
                if (!array_key_exists($part, $source)) {
                    break;
                }

                if ($i === count($parts) - 1) {
                    $ref[$part] = $source[$part];
                } else {
                    if (!isset($ref[$part]) || !is_array($ref[$part])) {
                        $ref[$part] = [];
                    }

                    $ref = &$ref[$part];
                    $source = $source[$part];
                }
            }
        }

        return $result;
    }
}
