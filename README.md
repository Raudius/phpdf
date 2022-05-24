A mediocre library for modifying PDF files.

## ❗ Important ❗
Be warned that this library is not production ready!
 - **QPDF dependant**  
   To use this library you need to have qpdf installed on your system.
 - **Performance**  
   Although some attention has been put to simple optimisations, this library is far from optimal particularly in terms of memory consumption. Additionally, it relies heavily on repeated read/writes to temp files.
 - **Compatibility**  
   I am only testing this library on Linux with qpdf >=v9.1.1.
 - **Security**  
   This library directly interfaces with the shell. Do NOT pass along user-defined values to any function on this library.

## 📖 How it works

### Loading PDFs

There are multiple ways to get a PDF file going, the simplest is to create a stub file (an empty PDF file ready to receive data):

```php
$pdf = Phpdf::create();
```

You can also load directly from an existing PDF. In this method you can specify whether you want operations performed on the pdf to overwrite the existing file (false by default).   
```php
$pdf = Phpdf::fopen('example.pdf', true);           // Overwrite = true
$pdf = Phpdf::fopen(fopen('example.pdf', 'rb'));    // Overwrite = false
```

You can also create a PDF directly from a raw PDF string, this is the least advisable method as it requires loading the entire file into memory:
```php 
$pdf = Phpdf::create(file_get_contents('example.pdf'));
```

### PDF operations

You can perform different operations on your `Phpdf` objects. You can specify the pdf object in the execution argument to apply the changes directly to that file.  
For more detailed information on each operation you can read their corresponding PHPdoc.
```php
// Combine 3 PDF files into 1
$pdf_merged = (new Merge([
  Phpdf::fopen('file1.pdf'),
  Phpdf::fopen('file2.pdf'),
  Phpdf::fopen('file3.pdf')
]))->execute();

// Keep just the 1st, 3rd and 4th pages.
(new Trim($pdf_merged, '1,3,4'))->execute($pdf_merged);
```

### Save PDF
Unless you specified to overwrite an input file you will need to save your resulting PDF manually:

```php
$pdf = Phpdf::create();
$pdf->saveAs('copy1.pdf');
$pdf->saveAs('copy2.pdf');
$pdf->saveAs('copy3.pdf');
```

## 🔓 License

There is no license attached to this code, because I have no desire to claim ownership of it.
If you wish to use this against your better judgement and require a license, you may use it under any fully permissive license (Beerware, WTFPL, CC0...)
