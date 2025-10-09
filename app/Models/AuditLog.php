<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'action',
        'model_type',
        'model_id',
        'old_values',
        'new_values',
        'changes',
        'user_id',
        'ip_address',
        'user_agent',
        'url',
        'description',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'changes' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // usuario que realizou a ação
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // model alvo da auditoria
    public function model()
    {
        return $this->morphTo();
    }

    // escopo para filtrar por modelo
    public function scopeForModel($query, $modelType, $modelId = null) 
    {
        $query->where('model_type', $modelType);

        if ($modelId) {
            $query->where('model_id', $modelId);
        }

        return $query;
    }

    // escopo para filtrar por ação 
    public function scopeAction($query, $action)
    {
        return $query->where('action', $action);
    }

    // formata as mudanças para exibição 
    public function getFormattedChangesAttribute()
    {
        if (empty($this->changes)) {
            return 'Nenhuma alteração específica';
        }

        $changes = [];
        foreach ($this->changes as $field => $change) {
            $changes[] = "{$field}: '{$change['old']}' -> '{$change['new']}'";
        }

        return implode(';' , $changes);
    }
}
