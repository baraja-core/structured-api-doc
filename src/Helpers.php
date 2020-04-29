<?php

declare(strict_types=1);

namespace Baraja\StructuredApi\Doc;


use Nette\Http\Request;

final class Helpers
{
	/**
	 * @throws \Error
	 */
	public function __construct()
	{
		throw new \Error('Class ' . get_class($this) . ' is static and cannot be instantiated.');
	}


	/**
	 * Return current API path by current HTTP URL.
	 * In case of CLI return empty string.
	 *
	 * @param Request $httpRequest
	 * @return string
	 */
	public static function processPath(Request $httpRequest): string
	{
		return trim(str_replace(rtrim($httpRequest->getUrl()->withoutUserInfo()->getBaseUrl(), '/'), '', (string) self::getCurrentUrl()), '/');
	}


	/**
	 * Return current absolute URL.
	 * Return null, if current URL does not exist (for example in CLI mode).
	 *
	 * @return string|null
	 */
	public static function getCurrentUrl(): ?string
	{
		if (!isset($_SERVER['REQUEST_URI'], $_SERVER['HTTP_HOST'])) {
			return null;
		}

		return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http')
			. '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	}


	/**
	 * Convert lots of comment styles to normalized multiline form.
	 *
	 * @param string $haystack
	 * @return string
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
	 *
	 * @param string $haystack
	 * @return string|null
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
	 *
	 * @param string $haystack
	 * @param string $annotation
	 * @param string|null $matchingPattern
	 * @return string|null
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
	 * @param string $haystack
	 * @param string $annotation
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


	/**
	 * @param string $name
	 * @return string
	 */
	public static function formatPresenterNameToUri(string $name): string
	{
		return trim((string) preg_replace_callback('/([A-Z])/', static function (array $match): string {
			return '-' . mb_strtolower($match[1], 'UTF-8');
		}, $name), '-');
	}
}