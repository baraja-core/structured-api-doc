<?php

declare(strict_types=1);

namespace Baraja\StructuredApi\Doc;


use Baraja\Url\Url;
use Nette\Http\Request;

final class Helpers
{

	/** @throws \Error */
	public function __construct()
	{
		throw new \Error('Class ' . get_class($this) . ' is static and cannot be instantiated.');
	}


	/**
	 * Return current API path by current HTTP URL.
	 * In case of CLI return empty string.
	 */
	public static function processPath(Request $httpRequest): string
	{
		return trim(str_replace(rtrim($httpRequest->getUrl()->withoutUserInfo()->getBaseUrl(), '/'), '', Url::get()->getCurrentUrl()), '/');
	}


	public static function isLocalRequest(): bool
	{
		return \in_array($_SERVER['REMOTE_ADDR'] ?? '', ['127.0.0.1', '::1'], true);
	}


	/**
	 * Convert lots of comment styles to normalized multiline form.
	 */
	public static function normalizeComment(string $haystack): string
	{
		$haystack = (string) preg_replace('/^\/\*+\s*/', '', $haystack);
		$haystack = (string) preg_replace('/\s*\*+\/$/', '', $haystack);
		$haystack = (string) preg_replace('/(?:^|\n)(?:\s*\*+\s*)+/', "\n", $haystack);

		return trim($haystack);
	}


	/**
	 * Match first comment lines (to first annotation) as user description.
	 */
	public static function findCommentDescription(string $haystack): ?string
	{
		if (preg_match('/^((?:.|\n)*?)(?:@|$)/', $haystack, $match)) {
			return trim($match[1]) ?: null;
		}

		return null;
	}


	/**
	 * Find best matching comment line by given annotation.
	 * In case of lots of annotations (for instance "param") you can use filtering by regex pattern.
	 * Method return trimmed section after matched annotation or null.
	 */
	public static function findCommentAnnotation(string $haystack, string $annotation, ?string $matchingPattern = null): ?string
	{
		foreach (explode("\n", $haystack) as $line) {
			if (preg_match('/^@(\S+)\s*(.*)$/', trim($line), $parser) && ($parser[1] ?? '') === $annotation) {
				if ($matchingPattern !== null) {
					if (preg_match('/^' . $matchingPattern . '$/', $parser[0])) {
						return $parser[0];
					}
					continue;
				}

				return $parser[2];
			}
		}

		return null;
	}


	/**
	 * @return string[]
	 */
	public static function findAllCommentAnnotations(string $haystack, string $annotation): array
	{
		$return = [];

		foreach (explode("\n", $haystack) as $line) {
			if (preg_match('/^@(\S+)\s*(.*)$/', trim($line), $parser) && ($parser[1] ?? '') === $annotation && ($content = trim($parser[2] ?? '')) !== '') {
				$return[] = $content;
			}
		}

		return $return;
	}


	public static function formatPresenterNameToUri(string $name): string
	{
		return trim((string) preg_replace_callback('/([A-Z])/', static function (array $match): string {
			return '-' . mb_strtolower($match[1], 'UTF-8');
		}, $name), '-');
	}
}
