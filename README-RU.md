# Ajax Bundle

Язык: \[ [English](./README.md) | Русский \]

## Описание

`kenny1911/ajax-bundle` — это Symfony-бандл, позволяющий в контроллере, помеченом атрибутом `#[Ajax]`:

- Возвращать DTO вместо объекта `Response`.
- Ответ из контроллера автоматически преобразовывать в JSON.
- Исключения, выбрасываемые в контроллере, преобразовывать в JSON.

Вместе с Symfony [Mapping Request Data](https://symfony.com/blog/new-in-symfony-6-3-mapping-request-data-to-typed-objects)
может использоваться как более простая альтернатива [FOS REST Bundle](https://github.com/FriendsOfSymfony/FOSRestBundle).

Дл сериализации используется пакет `symfony/serializer`. Сериализация происходит **только** в формат JSON.

## Установка

```bash
composer require kenny1911/ajax-bundle
```

Добавить `Kenny1911\AjaxBundle\AjaxBundle` в `bundles.php`.

## Использование

Просто добавьте атрибут `#[Ajax]` к методу контроллера, и возвращаемый DTO автоматически будет преобразован в JSON.

### Пример использования

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

В ответе клиент получит JSON-объект:

```json
{
    "id": 1,
    "title": "Post title"
}
```

## Совместимость

- PHP 8.1+
- Symfony 5.4+

## Лицензия

Этот проект распространяется под лицензией MIT. Подробности смотрите в файле [LICENSE](./LICENSE).
