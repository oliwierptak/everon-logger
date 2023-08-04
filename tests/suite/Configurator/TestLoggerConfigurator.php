<?php declare(strict_types = 1);

namespace EveronLoggerTests\Suite\Configurator;

class TestLoggerConfigurator
{
    use \Everon\Shared\Logger\Configurator\MonologLevelConfiguratorTrait;

    protected ?string $message;

    protected array $extra = [];

    protected array $context = [];

    protected string $delimiter = '] everon-logger.';

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): static
    {
        $this->message = $message;

        return $this;
    }

    public function getExtra(): array
    {
        return $this->extra;
    }

    public function setExtra(array $extra): static
    {
        $this->extra = $extra;

        return $this;
    }

    public function getContext(): array
    {
        return $this->context;
    }

    public function setContext(array $context): static
    {
        $this->context = $context;

        return $this;
    }

    public function getDelimiter(): string
    {
        return $this->delimiter;
    }
}
