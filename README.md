# Ajax Bundle

Language: \[ English | [Русский](./README-RU.md) \]

## Description

`kenny1911/ajax-bundle` is a Symfony bundle that allows controllers marked with the `#[Ajax]` attribute to:

- Return DTOs instead of `Response` objects.
- Automatically convert controller responses to JSON.
- Convert exceptions thrown in the controller to JSON responses.

Together with Symfony [Mapping Request Data](https://symfony.com/blog/new-in-symfony-6-3-mapping-request-data-to-typed-objects),
it can be used as a simpler alternative to [FOS REST Bundle](https://github.com/FriendsOfSymfony/FOSRestBundle).

The `symfony/serializer` package is used for serialization. Serialization is performed **only** in JSON format.

## Installation

```bash
composer require kenny1911/ajax-bundle
```

Add `Kenny1911\AjaxBundle\AjaxBundle` to `bundles.php`.

## Usage

Simply add the `#[Ajax]` attribute to a controller method, and the returned DTO will automatically be converted to JSON.

### Example Usage

```php
use Kenny1911\AjaxBundle\Attribute\Ajax;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class PostController
{
    public function __construct(
        private readonly PostRepository $postRepository,
    ) {}
    
    #[Ajax]
    public function getPost(string $id): Post
    {
        $post = $this->postRepository->find($id) ?? throw new NotFoundHttpException();
        
        return new Post(
            id: $post->getId(),
            title: $post->getTitle(),
        );
    }
}
```

The client will receive the following JSON response:

```json
{
    "id": 1,
    "title": "Post title"
}
```

## Compatibility

- PHP 8.1+
- Symfony 5.4+

## License

This project is licensed under the MIT License. See the [LICENSE](./LICENSE) file for details.
