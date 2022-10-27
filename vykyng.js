
const Schema = require('./schema.js')
const domain_schema = new Schema('pdxd8_domains','imports')
const charset_schema = new Schema('charset_ranges','imports')
const charset_ranges = {}
const ignore_charsets = [
  'Basic Latin',
  'General Punctuation',
  'Latin-1 Supplement'
]
const {Builder, By, Key, until} = require('selenium-webdriver');

(async function example() {
  let driver = await new Builder().forBrowser('firefox').build()
  //
  for (let i = 0; i < charset_schema.data.index.length; i++ ) {
    charset_ranges[charset_schema.data.index[i][0]] = {
      lower_bound : charset_schema.data.index[i][1],
      upper_bound : charset_schema.data.index[i][2]
    }
  }
  // Domian Loop
  //for (let i = 0; i < domain_schema.data.index.length; i++) {
  var domain_name = 'education-career-development'
  let sitemap_schema = new Schema('sitemaps/' + domain_name + '_page_inventory','exports')
  console.log('Auditing Site: ' + domain_name)
  // Sitemap Loop
  for (let ii = 0; ii < sitemap_schema.data.index.length; ii++) {
    var tally = {}
    let export_arr = []
    let export_str = ''
    let text = ''
    let page_url = sitemap_schema.data.index[ii][0].replace(/\"/g,'')
    //


    console.log('Crawling Page: ' + page_url)

    driver.get(page_url)
    text = await driver.findElement(By.css('body')).getText()

    //
    const char_arr = text.split('')
    for (let i = 0; i < char_arr.length;i++) {
      Object.keys(charset_ranges).forEach( function (charset_label) {
        //https://jrgraphix.net/r/Unicode/
        if (ignore_charsets.indexOf(charset_label)===-1) {
          if (char_arr[i].charCodeAt(0)>=parseInt(charset_ranges[charset_label].lower_bound,16) &&
            char_arr[i].charCodeAt(0)<=parseInt(charset_ranges[charset_label].upper_bound,16))
          {
            if (tally[charset_label]) { tally[charset_label]++ } else { tally[charset_label] = 1 }
          }
        }
      })
    }
    Object.keys(tally).forEach( function (charset_label) {
      export_arr.push([charset_label,tally[charset_label]])
    })
    if (export_arr.length) {
      console.log('exporting charset inventory for ' + page_url.replace('https://','').replace(/\//g,'-'))
      export_str = Schema.make_export_str(export_arr)
      charset_schema.export_csv(
        export_str,
        page_url.replace('https://','').replace(/\//g,'-') + '_charset_inventory',
        'exports/charset_inventory'
      )
    }
  } // ends Sitemap Loop
  //} // ends Domain Loop
  await driver.quit();
})();
