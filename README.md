Structured API Documentation
============================

Fully automated tool for documentation.

Idea
----

Imagine some API endpoint (implementing [Baraja Structured API](https://github.com/baraja-core/structured-api)) like this:

```php
/**
 * Common API endpoint for robust article manipulation.
 *
 * @endpointName Article manager
 */
final class ArticleEndpoint extends BaseEndpoint
{

   /**
    * @var ArticleManagerAccessor
    * @inject
    */
   public $articleManager;


   /**
    * @param string $locale in format "cs" or "en"
    * @param int $page real page number for filtering, 1 => first page ... "n" page
    * @param string|null $filterTitle filter by words in title?
    * @param string|null $filterFrom find all articles from this date
    * @param string|null $filterTo find all articles to this date
    */
   public function actionDefault(string $locale, int $page = 1, ?string $filterTitle = null, ?string $filterFrom = null, ?string $filterTo = null): void
   {
      // Here is some body...
```

You can simply type documentation to native PHP Doc blocks in your code and it will generate documentation automatically to HTML:

![Rendered documentation about Article](doc/sample-article.png)

For documentation simply open URI `/api-documentation` and your schema will be created automatically.
