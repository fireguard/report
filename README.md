# Fireguard Report

[![Build Status](https://travis-ci.org/fireguard/report.png)](https://travis-ci.org/fireguard/report)
[![Latest Stable Version](https://poser.pugx.org/fireguard/report/v/stable)](https://packagist.org/packages/fireguard/report)
[![Latest Unstable Version](https://poser.pugx.org/fireguard/report/v/unstable)](https://packagist.org/packages/fireguard/report)
[![Total Downloads](https://poser.pugx.org/fireguard/report/downloads)](https://packagist.org/packages/fireguard/report)
[![License](https://poser.pugx.org/fireguard/report/license)](https://packagist.org/packages/fireguard/report)
[![Code Climate](https://codeclimate.com/github/fireguard/report/badges/gpa.svg)](https://codeclimate.com/github/fireguard/report)

<!-- **Other languages for this documentation: [PORTUGUÊS](README_PT.md)**-->
O **Fireguard Report** é um pacote para gestão de relatórios em PHP que tem o intuito de auxiliar na exportação 
de informações em diversos formatos, como HTML e PDF, usando-se para isso de uma interface única, integrada e simples.

<div id='summary'/>
# Sumário 

- [Instalação](#install)
    - [Instalação e Atualização do PhantomJs](#install-phantom)
- [Como utilizar](#use)
    - [Gerando nosso primeiro relatório](#first-report)
    - [Cabeçalho e Rodapé](#footer-header)
    - [Exporters](#exporters)
        - [Métodos disponíveis em todos os Exports](#methods-exports)
        - [PdfExporter](#pdf-exporter)
        - [HtmlExporter](#html-exporter)
- [Laravel](#laravel)
    - [Registrando Service Provider](#laravel-register-provider)
    - [Publicando o arquivo de configuração](#laravel-publish-config)
    - [Exemplos de uso com Laravel (Dependency Injection)](#laravel-use)
- [Outros exemplos de uso](#examples)
    - [Gerando um relatório em PDF](#use-link-pdf)
    - [Gerando um relatório em HTML](#use-link-html)
    - [Boleto de exemplo gerado com este package](#use-link-boleto)


# <div id="install" />Instalação

O Fireguard Report pode ser instalado através do composer. 

Para que o package seja adicionado automaticamente ao seu arquivo composer.json execute o seguinte comando:

```bash
  composer require fireguard/report
```

ou se preferir, adicione o seguinte trecho manualmente:

```
{
  "require": {
    ...
    "fireguard/report": "^0.1"
  }
}
```

## <div id="install-phantom"/>Instalação e Atualização do PhantomJs 

Para gerar os arquivos PDF, este pacote utiliza-se do PhantomJs. Para a instalação e atualização do mesmo, sugerimos 
duas opções:

**1ª Opção:**  Adicionar as linhas abaixo no arquivo composer.json, dessa forma o processo de instalação e atualização 
acontecerá sempre que executar os comandos "composer install" ou "composer update".

```
  "scripts": {
    "post-install-cmd": [
      "PhantomInstaller\\Installer::installPhantomJS"
    ],
    "post-update-cmd": [
      "PhantomInstaller\\Installer::installPhantomJS"
    ]
  }
```

**2ª Opção:** Caso não deseje manter sempre na última versão PhantomJS, uma possibilidade é acrescentar um novo script 
no composer.json como demonstrado abaixo:

```
  "scripts": {
    "update-phantomjs": [
      "PhantomInstaller\\Installer::installPhantomJS"
    ]
  }
```

E executar sempre que quiser atualizar a versão do executável o seguinte comando ``composer run-script update-phantomjs``

Caso opte por essa opção, deverá executar ao menos a primeira vez para que o mesmo seja instalado.


# <div id="use"/>Como utilizar

## <div id="first-report"/>Gerando nosso primeiro relatório

A utilização desse pacote é bastante simples, precisaremos de dois objetos para gerarmos um arquivo final, o primeiro é 
Report, com ele definimos o conteúdo efetivo do relatório, o segundo é o Exporter, que recebe um Report e é responsável 
por tratar a informação e exportar para um arquivo final. 

Abaixo um exemplo simples para gerar um arquivo:

```php
    $report     = new \Fireguard\Report\Report('<h1>Report Title</h1>');
    $exporter   = new \Fireguard\Report\Exporters\PdfExporter();
    $file       = $exporter->generate($report);
```

Assim ao término da execução, na variável $file teremos o caminho real para o arquivo gerado.

## <div id="footer-header" />Cabeçalho e Rodapé

Para o HTML do cabeçalho e rodapé, duas variáveis estão disponíveis em exporters que usam paginação, como é o caso do 
PdfExporter, o **numPage** e o **totalPages**, que contém a página atual e o total de páginas do relatório respectivamente. 
Para acessa-las deve-se envolver as mesmas por "@{{ }}", assim será o conteúdo da mesma atualizado automaticamente.
Abaixo um exemplo simples que irá se utilizar do cabeçalho e rodapé;

```php 
  $html   = file_get_contents('report.html');
  
  $header = '<div style="text-align: center;font-size: 20px; border-bottom: 1px #eeeeee solid; padding: 1px; ">';
  $header.= '    <strong>THE MANAGEMENT REPORT TITLE</strong>';
  $header.= '</div>';

  $footer = '<div style="text-align: right;font-size: 10px; border-top: 1px #eeeeee solid; padding: 2px;">';
  $footer.= '    Page <span>@{{ numPage }} of @{{ totalPages }}</span>';
  $footer.= '</div>';
  
  $report = new \Fireguard\Report\Report($html, $header, $footer);
  $exporter = new \Fireguard\Report\Exporters\PdfExporter('.', 'report1-to-pdf');
  $file   = $exporter->generate($report);
  
```
Com esse exemplo acima encontraremos na variável **$file** o caminho para o arquivo PDF gerado;

## <div id="exporters" />Exporters

Como vimos nos exemplos anteriores, para a exportação do relatório é necessário uma classe Exporter. Um Exporter é na 
verdade uma classe especializada, que implementa uma interface Exporter e que é responsável por pegar um Report e o 
transformar em um arquivo finalizado. 

Nesse momento incluímos no pacote dois Exporters, um para HTML e um para PDF, é possível que futuramente novos 
Exporters estejam disponíveis, inclusive incentivamos que desenvolvam novos Exporters, e se possível, contribuam com o 
projeto, assim disponibilizamos para todos um leque maior de possibilidades.

### <div id="methods-exports" />Métodos disponíveis em todos os Exports

``getPath()``: Retorna o local onde o arquivo gerado será salvo;

``setPath($path, $mode = 0777)``: Define o local onde o arquivo deve ser salvo;

``getFileName()``: Retorna o nome do arquivo a ser salvo;

``setFileName($fileName)``: Define o nome do arquivo a ser salvo;

``getFullPath()``: Retorna o caminho completo com o nome do arquivo a ser gerado;

``compress($buffer)``: Retorna um string comprimida sem comentários ou quebra de linhas;

``configure(array $config)``: Define as configurações a serem aplicadas ao relatório atual;

``generate(ReportContract $report)``: Processa o relatório e retorna um caminho para o arquivo gerado;

Exemplo de uso com interface fluente:

```php
$report = new \Fireguard\Report\Report('<h1>Report Title</h1>');
$exporter = new \Fireguard\Report\Exporters\PdfExporter();
$file = $exporter
            ->setPath('.') // Define o salvamento para a pasta local
            ->setFileName('report.pdf') // Define como 'report.pdf' o nome do arquivo a ser gerado
            ->configure(['footer' => ['height' => '30px']) // Configura em 30px o tamanho do rodapé
            ->generate($report); // Gera o relatório
```

### <div id="pdf-exporter" />PdfExport

Para a exportação de arquivos no formato de **PDF**, além dos métodos padrões, alguns outros estão disponíveis, abaixo é 
listado alguns deles:

``getFormat()``: Retorna o formato do papel definido;

``setFormat($format)``: Define um formato de papel a ser exportado. (Formatos válidos: 'A4', 'A3', 'Letter')

``getOrientation()``: Retorna a orientação do papel definida;

``setOrientation($orientation)``: Define a orientação do papel a ser exportado. (Orientações válidas: 'landscape', 'portrait')

``getMargin()``: Retorna a margem do papel definida;

``setMargin($margin)``: Define a margem do papel a ser exportado.

``getBinaryPath()``: Retorna o caminho para o binário do PhantomJS na aplicação;

``setBinaryPath($binaryPath)``: Define o caminho para o arquivo binário do PhantomJS na aplicação.

``getCommandOptions()``: Retorna os parâmetros a serem executados com o PhantomJS para a exportação;

``setCommandOptions(array $options)``: Define os parâmetros a serem executados com o PhantomJS para a exportação.

``addCommandOption($option, $value)``: Adiciona um novo parâmetro a ser executado com o PhantomJS para a exportação;

``getHeaderHeight()``: Retorna o tamanho do cabeçalho definido;

``getFooterHeight()``: Retorna o tamanho do rodapé definido;


### <div id="html-exporter" /> HtmlExport

Para a exportação de arquivos no formato de **HTML**, além dos métodos padrões, alguns outros estão disponíveis, abaixo 
é listado alguns deles:

``saveFile($content)``: Salva o arquivo HTML e retorna o caminho completo para o arquivo gerado;

# <div id="laravel" /> Laravel

Os passos descritos abaixo são opcionais e apenas podem facilitar para quem pretente usar este package com o Laravel 5.

## <div id="laravel-register-provider" /> Registrando Service Provider

No arquivo de configuração ``config\app.php``, registre acima dos providers de sua aplicação como segue abaixo:

```
'providers' => [
    Fireguard\Report\Laravel\ReportServiceProvider::class
]
```

## <div id="laravel-publish-config" /> Publicando o arquivo de configuração

Para publicar o arquivo de configuração deve-se usar o seguinte comando:
```
php artisan vendor:publish --provider="Fireguard\Report\Laravel\ReportServiceProvider"
```

## <div id="laravel-use" /> Exemplos de uso com Laravel (Dependency Injection)

Com o registro do service provider, agora pode-se usar a injeção de dependência do Laravel para resolver os exporters,
já os trazendo prontos e configurados com o arquivo de configuração da aplicação. 

Para a injeção de dependência é disponibilidado três classes, sendo uma interface e duas classes concretas, a interface
por padrão é resolvida para a classe concreta PdfExporter, o que pode ser alterado no parâmetro ``default-exporter`` do 
arquivo de configuração ```report.php`` gerado na integração. Veja abaixo alguns exemplos de uso.

### <div id="laravel-injection-interface" /> Exporter Interface

```php
    public function index (\Fireguard\Report\Exporters\Exporter $exporter)
    {
        $html = view()->make('welcome')->render();
        $file = $exporter->generate(new Report($html));

        $headers = [
            'Content-type' => mime_content_type($file),
            'Content-Transfer-Encoding' => 'binary',
            'Content-Length' => filesize($file),
            'Accept-Ranges' => 'bytes'
        ];

        // Caso queira mostrar diretamente o arquivo
        return response()->make(file_get_contents($file), 200, $headers);

        // Caso deseja forçar o download
        // return response()->download($file, 'report.pdf', $headers);
    }
```

### <div id="laravel-injection-pdf" /> PdfExporter Class

```php
    public function index (\Fireguard\Report\Exporters\PdfExporter $exporter)
    {
        $html = view()->make('welcome')->render();
        $file = $exporter->generate(new Report($html));

        $headers = [
            'Content-type' => 'application/pdf',
            'Content-Transfer-Encoding' => 'binary',
            'Content-Length' => filesize($file),
            'Accept-Ranges' => 'bytes'
        ];

        // Caso queira mostrar diretamente o arquivo
        return response()->make(file_get_contents($file), 200, $headers);

        // Caso deseja forçar o download
        // return response()->download($file, 'report.pdf', $headers);
    }
```

### <div id="laravel-injection-html" /> HtmlExporter Class

```php
    public function index (\Fireguard\Report\Exporters\HtmlExporter $exporter)
    {
        $html = view()->make('welcome')->render();
        $file = $exporter->generate(new Report($html));

        // Caso queira mostrar diretamente o arquivo
        // return response()->make(file_get_contents($file), 200);

        //Caso queira forçar o download
        return response()->download($file, 'report.html', []);
    }
```

# <div id="examples" /> Outros exemplos de uso
<br />
- <a href="examples/report1-pdf.php" target="_blank" id="use-link-pdf"> Gerando um relatório em PDF</a>
- <a href="examples/report1-html.php" target="_blank" id="use-link-html"> Gerando um relatório em HTML</a>
- <a href="examples/report-boleto.pdf" target="_blank" id="use-link-boleto"> Boleto de exemplo gerado com este package</a>
<br />