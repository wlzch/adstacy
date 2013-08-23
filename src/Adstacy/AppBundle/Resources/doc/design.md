Design Guideline
================

## 3 Level Inheritance

Termedan menggunakan 3 level inheritance yaitu base, layout dan page.

1.  Base template

    Terletak di `app/Resources/views/base.html.twig` dan berisi struktur dasar untuk semua template lainnya. 

2.  Layout template

    Layout adalah template dasar dari setiap bundle. Misalnya, App akan memiliki layout yang berbeda dengan layout bundle lainnya. Setiap layout akan meng-extends base template.

3.  Page template

    Page template adalah template spesifik setiap page. Biasanya page template akan meng-extends layout template dari bundlenya. Misalnya halaman utama akan meng-extends layout dari AppBundle.

## Asset

Asset (image, css, javascript, less) akan diletakkan pada folder public dari setiap bundle. Misalnya less untuk AppBundle akan diletakkan pada AdstacyAppBundle/Resources/public/less. 

### Struktur Less dan Css

```
- less => berisi less yang bisa di include seperti mixins, variabel, utilities
    - module => berisi less untuk tampilan yang dapat digunakan berulang-ulang
    - base => berisi less untuk tampilan dasar yang akan diinclude di setiap halaman
    - layout => berisi less untuk tampilan per halaman
```

### Assetic

Adstacy menggunakan assetic untuk mengatur asset yaitu melakukan compiling dari less ke css, coffeescript ke javascript, combine css/js, minify css/js kemudian men-dump asset tersebut kedalam 1 atau lebih file sehingga request menjadi lebih sedikit.

Sesuai dengan 3 level inheritance yang telah dijelaskan sebelumnya, assets pada assetic juga dibagi menjadi 3 bagian yaitu base, layout dan page.

1.  Base assets
    
    Berisi assets yang akan digunakan disetiap tempat.

2.  Layout assets

    Berisi assets yang spesifik pada layout bundle tertentu.

3.  Page assets

    Berisi assets yang spesifik halaman tertentu.
