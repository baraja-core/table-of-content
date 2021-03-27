<?php

declare(strict_types=1);

namespace Baraja\TableOfContent;


final class Response
{
	/**
	 * @param array<string, string> $items (id => title)
	 */
	public function __construct(
		private string $original,
		private string $content,
		private ?string $title,
		private ?string $perex,
		private array $items,
	) {
		$this->content = trim($content);
		$this->title = trim($title ?? '') ?: null;
		$this->perex = trim($perex ?? '') ?: null;
	}


	public function __toString(): string
	{
		return $this->getContent();
	}


	public function getOriginal(): string
	{
		return $this->original;
	}


	public function getContent(): string
	{
		return $this->content;
	}


	public function getTitle(): ?string
	{
		return $this->title;
	}


	public function getPerex(): ?string
	{
		return $this->perex;
	}


	/**
	 * @return string[]
	 */
	public function getItems(): array
	{
		return $this->items;
	}
}
