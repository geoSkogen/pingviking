
const Schema = require('./schema.js');
const domain_schema = new Schema('pdxd8_domains','imports');
const charset_schema = new Schema('charset_ranges','imports');
const charset_ranges = {}
const {Builder, By, Key, until} = require('selenium-webdriver');


(async function example() {
  for (let i = 0; i < domain_schema.data.index.length; i++) {
    //console.log(domain_schema.data.index[i][0])
  }

  for (let i = 0; i < charset_schema.data.index.length; i++ ) {
    charset_ranges[charset_schema.data.index[i][0]] = {
      lower : charset_schema.data.index[i][1],
      upper : charset_schema.data.index[i][2]
    }
  }

  let driver = await new Builder().forBrowser('firefox').build();
  driver.get('http://www.pdx.edu/education-career-development');
  let text = await driver.findElement(By.css('body')).getText()

  await driver.quit();

  //let export_str = Schema.make_export_str(text)
  charset_schema.export_csv(text,'education-career-development','exports')


})();
