# Perfect Excerpt
Perfect Excerpt is a WordPress plugin that shortens your excerpts to whole sentences. It will break the text on the punctuation closest to the length specified. Set the excerpt length by adding the row below to *functions.php*. Valid break positions are dots, question marks and exclamation mark.

```
update_option('excerpt_length', 300);
```
The number 300 in this case is the maximum number of words that will be shown.

To bypass Perfect Excerpt you can get the full excerpt by using the $post object.
```
$post->post_excerpt;
```

Add this option to overside auto init.
```
update_option('perfect_excerpt_disable_auto_init', true);
```

Happy writing!