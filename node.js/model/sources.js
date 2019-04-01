//include the model (aka DB connection)
var db = require('../config/database');
var oai = require('../model/oai_pmh');
const yyyymmdd = require('yyyy-mm-dd');

//create class
var sources = {
	all : function(req, res) {
		res.send("OK");
	},

	ListAll : function(req, res) {
		section = 'source_source';
		dt = yyyymmdd();

		var rlt = db.query('SELECT * from ?? where jnl_scielo = 1 and ((jnl_oai_last_harvesting <> "' + dt + '") or (jnl_oai_token <> "")) order by jnl_oai_last_harvesting desc', [section], function(error, results, fields) {
			if (error) {
				console.log("Table Empty");
				return ("Empty");
			} else {
				rows = results.length;				
				if (rows > 0)
					{
						line = results[0];
						rst = oai.test(line.id_jnl, line);
						res.send("Harvesting: " + rst);
					} else {
						res.send("Harvesting: no sources");
					}
				return ("");
			}
		});
	},
	test : function(id, line) {
		console.log('teste');
	}
}

module.exports = sources;
