<?php

declare(strict_types=1);

namespace Baraja\TableOfContent;


use Nette\Utils\Strings;

final class ContentManager
{
	public function parse(string $html): Response
	{
		$items = [];
		$content = (string) preg_replace_callback(
			'/<h2>([^<]+)<\/h2>/',
			function (array $match) use (&$items): string {
				$id = Strings::webalize($match[1]);
				$items[$id] = strip_tags(trim($match[1]));

				return '<div id="' . $this->escapeHtmlAttr($id) . '" class="content-anchor"></div>' . $match[0];
			},
			$html,
		);
		$title = null;
		$pureContent = null;
		if (preg_match('/^((?:.|\n)*)<h1>([^<]+)<\/h1>((?:.|\n)*)$/', $content, $htmlTitleParser)) {
			$title = strip_tags(trim($htmlTitleParser[2] ?? ''));
			$pureContent = trim($htmlTitleParser[1] ?? '') . trim($htmlTitleParser[3] ?? '');
		}
		$perex = null;
		if (preg_match('/<p>([^<]+)<\/p>/', $content, $htmlPerexParser)) {
			$perex = strip_tags(trim($htmlPerexParser[1] ?? ''));
		}

		return new Response(
			$html,
			$content,
			$pureContent ?? $content,
			$title,
			$perex,
			$items,
		);
	}


	private function escapeHtmlAttr(string $s, bool $double = true): string
	{
		if (str_contains($s, '`') && strpbrk($s, ' <>"\'') === false) {
			$s .= ' '; // protection against innerHTML mXSS vulnerability nette/nette#1496
		}

		return htmlspecialchars($s, ENT_QUOTES | ENT_HTML5 | ENT_SUBSTITUTE, 'UTF-8', $double);
	}
}
