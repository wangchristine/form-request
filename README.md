# Lumen Form Request

> This package can use form request just as Laravel do.

# Installation

Install by composer

```bash
    $ composer require chhw/form-request
```

In `bootstrap/app.php`, you should:
1. uncomment `$app->withFacades();`
2. add `$app->register(CHHW\FormRequest\FormRequestServiceProvider::class);`

# Usage

> Just like the way to use Laravel !

### By structured file:

add something request like: `app/Http/Requests/CreatePost.php`，extends `CHHW\FormRequest\FormRequest`

> app/Http/Requests/CreatePost.php

```php
<?php

namespace App\Http\Requests;

use CHHW\FormRequest\FormRequest;

class CreatePost extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            "title" => "required|string",
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
             if (true) {
                 $validator->errors()->add('field', 'Something is wrong with this field!');
             }
        });
    }
}

```

> app/Http/Controllers/ExampleController.php:

```php
use App\Http\Requests\CreatePost;

class ExampleController extends Controller
{
    public function test(CreatePost $request)
    {
        dd($request->all());
        // ...
    }
}
```

### Or in controller:

> app/Http/Controllers/ExampleController.php

```php
use App\Rules\Uppercase;
use Illuminate\Http\Request;

class ExampleController extends Controller
{
    public function create(Request $request)
    {
        $request->validate(["author" => ["required", new Uppercase],]);
        // ...
    }
}
```

> app/Rules/Uppercase.php

```php
 <?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class Uppercase implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return strtoupper($value) === $value;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The :attribute must be uppercase.';
    }
}
```

# Customizing Localization Error Messages

> just like laravel !

add `resources/lang/en/validation.php`, and other country languages.

# Error handler:

> app/Exceptions/Handler.php

```php
public function render($request, Throwable $exception)
{
    if($exception instanceof ValidationException){
        return response()->json(["message" => $exception->getMessage(), "details" => $exception->errors()]);
    }
    return parent::render($request, $exception);
}
```
