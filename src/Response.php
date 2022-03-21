<?php

declare(strict_types=1);

namespace Baraja\TableOfContent;


final class Response implements \Stringable
{
	/**
	 * @param array<string, string> $items (id => title)
	 */
	public function __construct(
		private string $original,
		private string $content,
		private string $pureContent,
		private ?string $title,
		private ?string $perex,
		private array $items,
	) {
		$title = trim($title ?? '');
		$perex = trim($perex ?? '');
		$this->content = trim($content);
		$this->title = $title !== '' ? $title : null;
		$this->perex = $perex !== '' ? $perex : null;
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


	public function getPureContent(): string
	{
		return $this->pureContent;
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
	 * @return array<string, string>
	 */
	public function getItems(): array
	{
		return $this->items;
	}
}
