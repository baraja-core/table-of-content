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

				return sprintf('<div id="%s" class="content-anchor"></div>%s', $this->escapeHtmlAttr($id), $match[0]);
			},
			$html,
		);
		$title = null;
		$pureContent = null;
		if (preg_match('/<h1>([^<]+)<\/h1>/', $content, $htmlTitleParser) === 1) {
			$title = strip_tags(trim($htmlTitleParser[1] ?? ''));
			$pureContent = str_replace($htmlTitleParser[0] ?? '', '', $content);
		}
		$perex = null;
		if (preg_match('/<p>([^<]+)<\/p>/', $content, $htmlPerexParser) === 1) {
			$perex = strip_tags(trim($htmlPerexParser[1] ?? ''));
		}

		return new Response(
			original: $html,
			content: $content,
			pureContent: $pureContent ?? $content,
			title: $title,
			perex: $perex,
			items: $items,
		);
	}


	private function escapeHtmlAttr(string $s): string
	{
		if (str_contains($s, '`') && strpbrk($s, ' <>"\'') === false) {
			$s .= ' '; // protection against innerHTML mXSS vulnerability nette/nette#1496
		}

		return htmlspecialchars($s, ENT_QUOTES | ENT_HTML5 | ENT_SUBSTITUTE);
	}
}
