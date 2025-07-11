<?php

namespace App\Services\DependencyTest;

class RequestScopedService
{
    private array $actionHistory = [];
    private int $actionCount = 0;
    private string $createdAt;
    private string $requestId;

    public function __construct()
    {
        $this->createdAt = now()->format('H:i:s.u');
        $this->requestId = uniqid('req_', true);
        $this->actionHistory[] = "Service created at {$this->createdAt} for request {$this->requestId}";
    }

    public function performAction(string $action): void
    {
        $this->actionCount++;
        $timestamp = now()->format('H:i:s.u');
        $this->actionHistory[] = "Action #{$this->actionCount}: {$action} at {$timestamp}";
    }

    public function getStatus(): array
    {
        return [
            'created_at' => $this->createdAt,
            'request_id' => $this->requestId,
            'action_count' => $this->actionCount,
            'action_history' => $this->actionHistory,
            'instance_id' => spl_object_id($this),
            'object_hash' => spl_object_hash($this),
        ];
    }

    public function getInstanceInfo(): string
    {
        return "RequestScopedService created at {$this->createdAt} for {$this->requestId}";
    }
} 