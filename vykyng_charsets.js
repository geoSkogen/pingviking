
const Schema = require('./schema.js')
const domain_schema = new Schema('pdxd8_domains_partial','imports')
//https://jrgraphix.net/r/Unicode/
const charset_schema = new Schema('charset_ranges','imports')
const ignore_charsets = [
  'Basic Latin',
  'General Punctuation',
  'Latin-1 Supplement'
]
const charset_ranges = {}
//
const {Builder, By, Key, until} = require('selenium-webdriver');

(async function example() {
  let driver = await new Builder().forBrowser('firefox').build()
  // Organize the Unicode Ranges by Character set label
  for (let i = 0; i < charset_schema.data.index.length; i++ ) {
    charset_ranges[charset_schema.data.index[i][0]] = {
      lower_bound : charset_schema.data.index[i][1],
      upper_bound : charset_schema.data.index[i][2]
    }
  }
  // Domain Loop - iterate each domain in the configuration file
  for (let iii = 0; iii < domain_schema.data.index.length; iii++) {
    var domain_name = domain_schema.data.index[iii][0].replace('pdx.edu/','')
    let sitemap_schema = new Schema('sitemaps/' + domain_name + '_page_inventory','exports')
    console.log('Auditing Site: ' + domain_name)
    // Sitemap Loop - iterate each URL in the current domain's sitemap
    for (let ii = 0; ii < sitemap_schema.data.index.length; ii++) {
      var tally = {}
      let export_arr = []
      let export_str = ''
      let text = ''
      let page_url = sitemap_schema.data.index[ii][0].replace(/\"/g,'')
      //
      console.log('Crawling Page: ' + page_url)
      //
      driver.get(page_url)
      text = await driver.findElement(By.css('body')).getText()
      //
      const char_arr = text.split('')
      // Text Loop - iterate each printable character on the page
      for (let i = 0; i < char_arr.length;i++) {
        // Character Set Loop - iterate each character-subset's structured data
        Object.keys(charset_ranges).forEach( function (charset_label) {
          // bypass standard character sets
          if (ignore_charsets.indexOf(charset_label)===-1) {
            if (char_arr[i].charCodeAt(0)>=parseInt(charset_ranges[charset_label].lower_bound,16) &&
              char_arr[i].charCodeAt(0)<=parseInt(charset_ranges[charset_label].upper_bound,16))
            {
              if (tally[charset_label]) { tally[charset_label]++ } else { tally[charset_label] = 1 }
            }
          }
        }) // end Character Set Loop
      } // end Text Loop
      Object.keys(tally).forEach( function (charset_label) {
        export_arr.push([charset_label,tally[charset_label]])
      })
      if (export_arr.length) {
        //
        console.log('exporting charset inventory for ' + page_url.replace('https://','').replace(/\//g,'-'))
        //
        export_str = Schema.make_export_str(export_arr)
        charset_schema.export_csv(
          export_str,
          page_url.replace('https://','').replace(/\//g,'--') + '_charset_inventory',
          'exports/charset_inventory'
        )
      }
    } // ends Sitemap Loop
  } // ends Domain Loop
  await driver.quit();
})();
