<?php
declare(strict_types=1);

namespace App\IoC;

final class IoC
{
    /**
     * Реестр сервисов:
     * [scopeId => [id => Definition]]
     *
     * @var array<string, array<string, Definition>>
     */
    private array $registry = ['_global' => []];

    /**
     * Текущий скоуп.
     */
    private string $currentScope = '_global';

    /**
     * Единый метод для регистрации, разрешения и управления скоупами.
     *
     * @template T
     * @param string $key Команда или id сервиса
     * @param mixed ...$args Аргументы команды или параметры для фабрики
     * @return CommandInterface<T>
     */
    public function resolve(string $key, ...$args): CommandInterface
    {
        return match ($key) {
            'IoC.Register' => $this->cmdRegister(...$args),
            'Scopes.New' => $this->cmdScopesNew(...$args),
            'Scopes.Current' => $this->cmdScopesCurrent(...$args),
            default => $this->cmdResolve($key, ...$args)
        };
    }

    /**
     * Регистрация сервиса.
     *
     * @param string $id Идентификатор сервиса
     * @param callable $factory Фабрика создания сервиса
     * @param string|null $scope (опционально) скоуп, куда регистрировать сервис
     * @return CommandInterface<void>
     */
    private function cmdRegister(string $id, callable $factory, ?string $scope = null): CommandInterface
    {
        return new Command(function() use ($id, $factory, $scope): void {
            $scopeKey = $scope ?? $this->currentScope;
            if (!isset($this->registry[$scopeKey])) {
                $this->registry[$scopeKey] = [];
            }
            $this->registry[$scopeKey][$id] = new Definition($factory);
        });
    }

    /**
     * Создание нового скоупа.
     *
     * @param string $scopeId
     * @return CommandInterface<void>
     */
    private function cmdScopesNew(string $scopeId): CommandInterface
    {
        return new Command(function() use ($scopeId): void {
            if ($scopeId === '') {
                throw new \InvalidArgumentException("Scope id must be non-empty string");
            }
            $this->registry[$scopeId] = [];
        });
    }

    /**
     * Установка текущего скоупа.
     *
     * @param string $scopeId
     * @return CommandInterface<void>
     */
    private function cmdScopesCurrent(string $scopeId): CommandInterface
    {
        return new Command(function() use ($scopeId): void {
            if ($scopeId === '') {
                throw new \InvalidArgumentException("Scope id must be non-empty string");
            }
            if (!isset($this->registry[$scopeId])) {
                $this->registry[$scopeId] = [];
            }
            $this->currentScope = $scopeId;
        });
    }

    /**
     * Разрешение сервиса по id и аргументам.
     *
     * @template T
     * @param string $id
     * @param mixed ...$args
     * @return CommandInterface<T>
     */
    private function cmdResolve(string $id, ...$args): CommandInterface
    {
        return new Command(function() use ($id, $args) {
            // Ищем сначала в текущем скоупе, затем в глобальном
            $scopesToTry = [$this->currentScope];
            if ($this->currentScope !== '_global') {
                $scopesToTry[] = '_global';
            }
            foreach ($scopesToTry as $scope) {
                if (isset($this->registry[$scope][$id])) {
                    $def = $this->registry[$scope][$id];
                    return $def->create($args);
                }
            }
            throw new \RuntimeException("Service with id '{$id}' not found in scope '{$this->currentScope}' or global");
        });
    }

    /**
     * Сброс контейнера (только для тестов).
     */
    public function reset(): void
    {
        $this->registry = ['_global' => []];
        $this->currentScope = '_global';
    }
}