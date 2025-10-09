<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

trait Auditable
{
    public static function bootAuditable(): void
    {
        static::created(function (Model $model) {
            $model->logAction('created', null, $model->getAttributes());
        });

        static::updated(function (Model $model) {
            $changes = $model->getChanges();
            $original = $model->getOriginal();

            $formattedChanges = [];
            foreach ($changes as $field => $newValue) {
                // ignorar campos de timestamp automáticos
                if (in_array($field, ['updated_at', 'created_at'])) {
                    continue;
                }

                $formattedChanges[$field] = [
                    'old' => $original[$field] ?? null,
                    'new' => $newValue,
                ];
            }

            $model->logAction('updated', $original, $model->getAttributes(), $formattedChanges);
        });

        static::deleted(function (Model $model) {
            $model->logAction('deleted', $model->getOriginal(), null);
        });
    }

    // Registrar ação na audit log

    protected function logAction(
        string $action,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?array $changes = null
    ): void {
        // não logar se não houver mudanças relevantes (após filtrar timestamps)
        if ($action == 'updated' && empty($changes)) {
            return;
        }

        $description = $this->getAuditDescription($action);

        AuditLog::create([
            'action' => $action,
            'model_type' => get_class($this),
            'model_id' => $this->getKey(),
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'changes' => $changes,
            'user_id' => Auth::id(),
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'url' => Request::fullUrl(),
            'description' => $description,
        ]);
    }

    // Gerar descrição para a ação

    protected function getAuditDescription(string $action) 
    {
        $modelName = class_basename($this);

        return match($action) {
            'created' => "Novo {$modelName} criado",
            'updated' => "{$modelName} atualizado",
            'deleted' => "{$modelName} excluído",
            default => "Ação '{$action}' realizada em {$modelName}",
        };
    }

    // relaciomento principal com logs de auditoria 
    public function auditLogs()
    {
        return $this->morphMany(AuditLog::class, 'model');
    }

    // ultimo log de auditoria
    public function latestAuditLog()
    {
        return $this->morphOne(AuditLog::class, 'model')->latest();
    }

    // logs de criação
    public function creationLogs()
    {
        return $this->auditLogs()->action('created');
    }

    // logs de atualização
    public function updateLogs()
    {
        return $this->auditLogs()->action('updated');
    }

    public function deletionLogs()
    {
        return $this->auditLogs()->action('deleted');
    }
}
