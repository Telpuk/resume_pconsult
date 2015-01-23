<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="ru">
<head>
	<meta charset="utf-8">
	<link rel="stylesheet" href="css.css">
</head>
<body>
<div id="professionalAreaBlock"></div>
<span id="profArea">Указать профобласти</span>

<div id="wrapperProfessArea">
	<div id="blockBlack"></div>

	<div id="blockArea">
		<div class="wrapperHeader">
			<p>Выбор сфер деятельности</p>
			<p><input type="search" id="text-search"  placeholder="Быстрый поиск" ></p>
			<p>Выбор сфер деятельности</p>
		</div>

		<div id="listProfessArea">
			<ul></ul>
		</div>

		<div class="wrapperFooter">
			<h1 id="enter">Выбрать</h1>
			<h1 id="cancel">Отменить</h1>
		</div>
		<div id="closeGif"></div>
	</div>
</div>
</body>

<script id="professionalAreaBlock-template" type="text/x-handlebars-template">
	<p  data-professional-area-id="{{id}}">{{title}}<span class="closeBlock"></span></p>
	<ul>
		{{#each object}}
		<li data-professional-area-children-id ={{this.id}}>
			<input type="hidden" name="professional_area[]" value="">
			{{this.title}}<span class="closeBlock"></span>
		</li>
		{{/each}}
	</ul>
</script>

<script id="professionalArea-template" type="text/x-handlebars-template">
	{{#if object}}
	{{#each object}}
	<li class="selector_node selector_close {{../status}}">
		<span>{{this.title}}</span>
		<ul {{#if ../status}}style='display:block'{{else}}style='display:none'{{/if}} >
		{{#each this.children}}
			<li><input data-professional-area-children-id ={{this.id}}  data-professional-area-id="{{../this.id}}" type="checkbox" value="{{this.title}}">{{this.title}}</li>
		{{/each}}
	</ul>
	</li>
	{{/each}}
	{{else}}
	<li>Совпадений не найдено</li>
	{{/if}}
</script>

<script src="public/js/vendor/jquery-2.1.1.min.js"></script>
<script src="public/js/source/handlebars-v2.0.0.js"></script>
<script src="highlight.js"></script>
<script src="js.js"></script>
</html>

