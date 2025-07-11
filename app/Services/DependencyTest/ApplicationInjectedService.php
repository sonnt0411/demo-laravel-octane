<?php

namespace App\Services\DependencyTest;

use Illuminate\Contracts\Foundation\Application;

class ApplicationInjectedService
{
    private Application $app;
    private static int $instanceCount = 0;
    private int $instanceId;
    private int $actionCount = 0;
    
    public function __construct(Application $app)
    {
        $this->app = $app;
        self::$instanceCount++;
        $this->instanceId = self::$instanceCount;
    }
    
    public function getInstanceId(): int
    {
        return $this->instanceId;
    }
    
    public function getApplicationObjectId(): string
    {
        return spl_object_hash($this->app);
    }
    
    public function getApplicationInfo(): array
    {
        return [
            'app_object_id' => $this->getApplicationObjectId(),
            'app_class' => get_class($this->app),
            'service_instance_id' => $this->instanceId,
            'service_object_id' => spl_object_hash($this),
            'action_count' => $this->actionCount,
            'is_singleton_in_container' => $this->app->resolved(self::class)
        ];
    }
    
    public function performAction(): void
    {
        $this->actionCount++;
    }
    
    public function getActionCount(): int
    {
        return $this->actionCount;
    }
    
    public static function getTotalInstanceCount(): int
    {
        return self::$instanceCount;
    }
    
    public function testApplicationFeatures(): array
    {
        return [
            'environment' => $this->app->environment(),
            'debug_mode' => $this->app->hasDebugModeEnabled(),
            'is_running_in_console' => $this->app->runningInConsole(),
            'app_version' => $this->app->version(),
            'base_path' => $this->app->basePath(),
        ];
    }
} 