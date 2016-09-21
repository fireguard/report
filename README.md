# Fireguard Report

[![Build Status](https://travis-ci.org/fireguard/report.png)](https://travis-ci.org/fireguard/report)
[![Latest Stable Version](https://poser.pugx.org/fireguard/report/v/stable)](https://packagist.org/packages/fireguard/report)
[![Latest Unstable Version](https://poser.pugx.org/fireguard/report/v/unstable)](https://packagist.org/packages/fireguard/report)
[![Total Downloads](https://poser.pugx.org/fireguard/report/downloads)](https://packagist.org/packages/fireguard/report)
[![License](https://poser.pugx.org/fireguard/report/license)](https://packagist.org/packages/fireguard/report)
[![Code Climate](https://codeclimate.com/github/fireguard/report/badges/gpa.svg)](https://codeclimate.com/github/fireguard/report)


O Fireguard Report é um pacote para gestão de relatórios em diversos formatos.

## Instalação

Fireguard Report pode ser instalado através do composer. 
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

# Instalação e Atualização do PhanthomJs

É sugerido duas formas para a instalação e atualização do PhanthomJS. São elas:

**1ª Opção:**  Adicionar as seguintes linhas no arquivo composer.json, dessa forma o processo de instalação e atualização 
acontecerá sempre que executar os comandos "composer install" e "composer update".

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

**2ª Opção:** Caso não deseje manter sempre na ultima versão PhantomJS, uma possibilidade é acrescentar um novo script 
no composer.json como demonstrado abaixo:

```
  "scripts": {
    "update-phantomjs": [
      "PhantomInstaller\\Installer::installPhantomJS"
    ]
  }
```

E executar sempre que quiser atualizar a versão do executável o seguinte comando ``composer run-script update-phantomjs``



# Cabeçalho e Rodapé

Para o html do cabeçalho e rodapé duas variáveis estão disponíveis em exporters que usam paginação, como é o caso do 
PdfExporter, o **numPage** e o **totalPages**, que contém a página atual e o total de páginas do relatório respectivamente. Para 
acessa-las deve-se envolver as mesmas por "@{{ }}", assim será o conteúdo da mesma será atualizado automaticamente.
Criar um exemplo simples que irá se utilizar do rodapé;

```php 
  $html = file_get_contents('report.html');
  
  $header = '<div style="text-align: center;font-size: 20px; border-bottom: 1px #eeeeee solid; padding: 1px; ">';
  $header.= '    <strong>THE MANAGEMENT REPORT TITLE</strong>';
  $header.= '</div>';

  $footer = '<div style="text-align: right;font-size: 10px; border-top: 1px #eeeeee solid; padding: 2px;">';
  $footer.= '    Page <span>@{{ numPage }} of @{{ totalPages }}</span>';
  $footer.= '</div>';
  
  $report = new \Fireguard\Report\Report($html, $header, $footer);
  $exporter = new \Fireguard\Report\Exporters\PdfExporter('.', 'report1-to-pdf');
  $file = $exporter->setOrientation('landscape')->generate($report);
  
```

Com esse exemplo acima encontraremos na variável **$file** o caminho para o arquivo PDF gerado;