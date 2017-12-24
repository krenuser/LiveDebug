ace.define("ace/mode/orasql", ["require", "exports", "module", "ace/lib/oop", "ace/mode/text", "ace/tokenizer", "ace/mode/sql_highlight_rules", "ace/range"], function(a, b, c) {
	var d = a("../lib/oop"),
		e = a("./text").Mode,
		f = a("../tokenizer").Tokenizer,
		g = a("./sql_highlight_rules").SqlHighlightRules,
		h = a("../range").Range,
		i = function() {
			this.$tokenizer = new f((new g).getRules())
		};
	d.inherits(i, e), function() {
		this.toggleCommentLines = function(a, b, c, d) {
			var e = !0,
				f = [],
				g = /^(\s*)--/;
			for (var i = c; i <= d; i++) if (!g.test(b.getLine(i))) {
				e = !1;
				break
			}
			if (e) {
				var j = new h(0, 0, 0, 0);
				for (var i = c; i <= d; i++) {
					var k = b.getLine(i),
						l = k.match(g);
					j.start.row = i, j.end.row = i, j.end.column = l[0].length, b.replace(j, l[1])
				}
			} else b.indentRows(c, d, "--")
		}
	}.call(i.prototype), b.Mode = i
}), ace.define("ace/mode/sql_highlight_rules", ["require", "exports", "module", "ace/lib/oop", "ace/lib/lang", "ace/mode/text_highlight_rules"], function(a, b, c) {
	var d = a("../lib/oop"),
		e = a("../lib/lang"),
		f = a("./text_highlight_rules").TextHighlightRules,
		g = function() {
		      var keywordsarr = 
                "select|from|where|and|or|group|by|order|limit|offset|having|"+
                "as|case|when|else|end|type|left|right|join|on|outer|desc|asc|"+
                "rownum|partition|over|within|declare|begin|alter|table|update|"+
                "set|insert|into|index|unique|add|modify|drop|truncate|procedure|"+
                "function|in|create|replace|trigger|package|merge|using|after|for|each|"+
                "row|values|nextval|not|matched|then|delete|inner|commit|execute|immediate|"+
                "column|format|union|all|with|system|session|fetch|first|rows|only|distinct";
              var funcs = "count|min|max|avg|sum|rank|now|coalesce|dense_rank|listagg|"+
                "round|trunc|upper|lower";
			var a = e.arrayToMap(keywordsarr.split("|")),
				b = e.arrayToMap("true|false|null".split("|")),
				c = e.arrayToMap(funcs.split("|"));
			this.$rules = {
				start: [{
					token: "comment",
					regex: "--.*$"
				},
				{
					token: "string",
					regex: '".*"'
				},
				{
					token: "string",
					regex: "'.*'"
				},
				{
					token: "constant.numeric",
					regex: "[+-]?\\d+(?:(?:\\.\\d*)?(?:[eE][+-]?\\d+)?)?\\b"
				},
				{
					token: function(d) {
						return d = d.toLowerCase(), a.hasOwnProperty(d) ? "keyword" : b.hasOwnProperty(d) ? "constant.language" : c.hasOwnProperty(d) ? "support.function" : "identifier"
					},
					regex: "[a-zA-Z_$][a-zA-Z0-9_$]*\\b"
				},
				{
					token: "keyword.operator",
					regex: "\\+|\\-|\\/|\\/\\/|%|<@>|@>|<@|&|\\^|~|<|>|<=|=>|==|!=|<>|="
				},
				{
					token: "paren.lparen",
					regex: "[\\(]"
				},
				{
					token: "paren.rparen",
					regex: "[\\)]"
				},
				{
					token: "text",
					regex: "\\s+"
				},
                {
					token: "comment",
					regex: "\\/\\*",
					next: "comment"
				}                
                ],
				comment: [{
					token: "comment",
					regex: ".*?\\*\\/",
					next: "start"
				},
				{
					token: "comment",
					regex: ".+"
				}]
			}
		};
	d.inherits(g, f), b.SqlHighlightRules = g
})