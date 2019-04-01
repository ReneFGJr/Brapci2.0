/******/
var db = require('../config/database');
var xml2js = require('xml2js');
const yyyymmdd = require('yyyy-mm-dd');
const fs = require("fs");

var oai = {
	test : function(id, line) {
		console.log('Function OAI-PMH test ' + line.jnl_name);
		dt = Date();
		dt = dt.toLocaleString();
		var sx = '';
		var sx = sx + '>> ' + line.id_jnl + String.fromCharCode(13) + String.fromCharCode(10);
		var sx = sx + '>> ' + line.jnl_url_oai + String.fromCharCode(13) + String.fromCharCode(10);
		var sx = sx + '>> ' + line.jnl_oai_set + String.fromCharCode(13) + String.fromCharCode(10);
		var sx = sx + '>> ' + dt + String.fromCharCode(13) + String.fromCharCode(10);
		console.log(sx);

		//oai.UpdateHarvesting(line.id_jnl);
		oai.ListSets(line);
		return (line.jnl_name);
	},
	Url : function(line, verb) {
		// http://www.scielo.br/oai/scielo-oai.php?verb=ListIdentifiers&metadataPrefix=oai_dc_openaire&set=0103-3786&resumptionToken=HR__S0103-37862003000200008:0103-3786:::oai_dc_openaire
		//&resumptionToken=
	},
	ListSets : function(line) {
		url = line.jnl_url_oai;
		if (line.jnl_scielo == 1) {
			var sets = line.jnl_oai_set;
			console.log('Harvesing Scielo ' + url + ' ' + sets);
			var parser = new xml2js.Parser();

			/* method  */
			console.log("Method #0");
			var xmlfile = '../node.js/oai/scielo-oai.php.xml';
			txt = fs.readFile(xmlfile, "utf-8", function(error, text) {
				parser.parseString(text, function(err, result) {
					rlt = result['OAI-PMH']['ListIdentifiers'];
					rlt2 = rlt[0]['header'];
					token = result['OAI-PMH']['ListIdentifiers'];
					tk = rlt[0]['resumptionToken'][0];
					console.log("Token: " + tk);
					console.log('Total de registros: ' + rlt2.length);
					for (var i = 0,
					    len = rlt2.length; i < len; i++) {
						ln = rlt2[i]['identifier'][0];
						console.log('#' + i + ': ' + ln);
					}
				});
			});
			return ("");
		} else {
			console.log('Harvesing ' + url);
		}

	},
	UpdateOaiToken : function(id, token) {
		section = 'source_source';
		dt = yyyymmdd();
		console.log(dt);
		var rlt = db.query('update ?? set jnl_oai_last_harvesting = "' + dt + '", jnl_oai_token = "' + token + '" where id_jnl = ' + id, [section], function(error, results, fields) {
			if (error) {
				console.log("Update Error " + error);
			}
			return ("");
		});
		console.log("Update [" + id + "] in " + dt);
	},
	UpdateHarvesting : function(id) {
		section = 'source_source';
		dt = yyyymmdd();
		console.log(dt);
		var rlt = db.query('update ?? set jnl_oai_last_harvesting = "' + dt + '" where id_jnl = ' + id, [section], function(error, results, fields) {
			if (error) {
				console.log("Update Error " + error);
			}
			return ("");
		});
		console.log("Update [" + id + "] in " + dt);
	}
}

module.exports = oai;
