# Routers

- [Introdução](#introduction)
- [Configuração](#config)
- [Tipos](#types)
 - [Literal](#literal)
 - [Segment](#segment)
 - [Regex](#regex)

<a name="introduction"></a>
## Introdução

Routers é um mecanismo para interpretar as requisições HTTP que são gerados. Com o Router você pode basicamente mapear e direcionar para o código a onde a quela requisição deve ser levada e oque é argumento ou não

<a name="config"></a>
## Configuração

A configuração de um roteamente é simples com os seguintes tendo em mente as seguintes propriedades:

<table>
	<tr>
		<th>Propriedade</th>
		<th>Tipo</th>
		<th>Regra</th>
		<th width="100%" align="left">Descrição</th>
	</tr>
	<tr>
		<td>map</td>
		<td>string</td>
		<td>requirido</td>
		<td>Mapeamento</td>
	</tr>
		<tr>
		<td>defaults</td>
		<td>array</td>
		<td>opcional</td>
		<td>Argumentos defaults</td>
	</tr>
		<tr>
		<td>options</td>
		<td>array</td>
		<td>opcional</td>
		<td>Opções para Informações extra para o mapeamento seu tipo</td>
	</tr>
		<tr>
		<td>type</td>
		<td>string</td>
		<td>opcional</td>
		<td>Default: Rabbit\Routing\Mapping\Literal </td>
	</tr>
</table>

Na propriedade `defaults` temos 5 argumentos que são tipos comportamentais pois as mesmas influenciam nos aspectos do framework:

<table>
	<tr>
		<th>Argumento</th>
		<th>Tipo</th>
		<th width="100%" align="left">Descrição</th>
	</tr>
	<tr>
		<td>module</td>
		<td>string</td>
		<td>Determina que módulo você está solicitando [Default: application]</td>
	</tr>
		<tr>
		<td>namespace</td>
		<td>string</td>
		<td>Determina que namespace você está solicitando [Default: main]</td>
	</tr>
		<tr>
		<td>controller</td>
		<td>string</td>
		<td>Determina que controller você está solicitando [Default: index]</td>
	</tr>
		<tr>
		<td>action</td>
		<td>string</td>
		<td>Determina que action você está solicitando [Default: index]</td>
	</tr>
	</tr>
		<tr>
		<td>_format</td>
		<td>string</td>
		<td>Informa que format [Default: html]</td>
	</tr>
</table>

**Yaml:**

Podemos definir um Router através do arquivo `router.yml` dentro da pasta do módulo ex:
	
	/root
	  application
	    Modules
	      Application
	        router.yml

Onde mapeamos da seguinte forma:

```yaml
nome_mapeamento:
 type: ''
 map: ''
 defaults:
  arg1: 'value1'
  namespace: 'main'
 options:
  option1:
   arg1: ''
```

Um exemplo de mapeamento:

```yaml
# Mapeamento utilizando como default o tipo Literal
MeuRouterArtigo:
 map: '/artigo'
 defaults:
  action: 'list'
  module: 'artigo'
  
# Mapeamento utilizando como tipo Segment
Modulox\Namespacey\Nomez:
 type: 'Rabbit\Routing\Mapping\Segment'
 map: '/artigo/:id'
 defaults:
  action: 'view'
  module: 'artigo'
 options:
  requirements:
   id: '[0-9]+' # só irá combinar com esse router se o id do tipo numeral
```


<a name="types"></a>
## Tipos

Rabbit temos 3 tipos de Routers Literal, Segment e Regex cada 1 tem um peso sobre o outro em termos de peso temos:

<table>
	<tr>
		<th>#</th>
		<th>Tipo</th>
		<th>Força</th>
	</tr>
	<tr>
		<td>1</td>
		<td>Literal</td>
		<td>Forte</td>
	</tr>
	<tr>
		<td>2</td>
		<td>Segment</td>
		<td>Medio</td>
	</tr>
	<tr>
		<td>3</td>
		<td>Regex</td>
		<td>Fraco</td>
	</tr>
</table>

Assim sendo: Mapeamento do tipo Literal sobrepoem as de Segment e a de Segment as de Regex.

<a name="literal"></a>
### Literal

O Roteramento do tipo `Literal` é basicamente conforme sua tradução "ao pé da letra", ele só irá fazer a combinção com o roteamento se a requisião for exatamente o que você solicitou.


Ex:

	REQUEST site.com.br/usuarios

RouterMap:
	
```yaml
Modulox\Namespacey\Usuarios:
 map: '/usuarios'
 defaults:
  controller: 'usuario'
```

