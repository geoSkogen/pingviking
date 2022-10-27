class Schema {
  constructor( filename, path ) {
    this.fs = require("fs-extra")
    this.data = this.import_csv( filename, path )
  }

  import_csv( import_filename, dir_path ) {
    var buffer = ""
    var slug_arr = []
    var data_arr = []
    var stage_obj = {}
    var export_arr = []
    var keys = []
    var data = this.fs.readFileSync('./' + dir_path + '/'+ import_filename + ".csv")
    for (var i = 0; i < data.length; i++) {
      buffer += String.fromCharCode(data[i])
    }
    buffer = buffer.replace(/\r\n/g,'\n')
    slug_arr = buffer.split("\n")
    for (var i = 0; i < slug_arr.length; i++) {
      data_arr[i] = slug_arr[i].split(",")
      stage_obj = {}
      for (var ii = 0; ii < data_arr[i].length; ii++) {
        if (i === 0) {
          keys.push(data_arr[i][ii])
        } else {
          stage_obj[keys[ii]] = data_arr[i][ii]
        }
      }
      if (i != 0) { export_arr.push(stage_obj) }
    }
    return { assoc: export_arr, index: data_arr }
  }

  static get_labeled_columns( data_arr ) {
    var keys =  []
    var result = []
    for (let row_index = 0; row_index < data_arr.length; row_index++) {
      for (let i = 0; i < data_arr[row_index].length; i++) {
        if (row_index === 0) {
          result[data_arr[row_index][i]] = []
          keys.push(data_arr[row_index])
        } else {
          if (data_arr[row_index][i]) {
            result[keys[i]].push(data_arr[row_index][i])
          }
        }
      }
    }
    return result
  }

  static get_labeled_rows( data_arr ) {
    var key = ''
    var valid_data = []
    var result = []
    for (let row_index = 0; row_index < data_arr.length; row_index++) {
      var valid_data = []
      for (let i = 0; i < data_arr[row_index].length; i++) {
        if (i === 0) {
          key = data_arr[row_index][i]
        } else {
          if (data_arr[row_index][i]) {
            valid_data.push(data_arr[row_index][i])
          }
          if (i === data_arr[row_index].length-1) {
            result[key] = valid_data
          }
        }
      }
    }
    return result
  }

  static get_indexed_rows( data_arr ) {
    var key = ''
    var valid_data = []
    var result = new Array
    for (let row_index = 0; row_index < data_arr.length; row_index++) {
      for (let i = 0; i  < data_arr[row_index].length; i++) {
        valid_data = []
        key = i
        if (data_arr[row_index]) {
          valid_data.push(data_arr[row_index])
        }
        if (i === data_arr[row_index].length-1) {
          result[key] = valid_data
        }
      }
    }
    return result
  }

  table_lookup( col, row ) {
    var result = false
    if ( (col || col === 0) && (row || row === 0) ){
      if (this.data.index[row][col]) {
        result = this.data.index[row][col]
      }
    }
    return result
  }

  static make_export_str( data ) {
    var export_str = new String
    var keys = new Array
    for (var i = 0; i < data.length; i++) {
      if (Array.isArray(data[i])) {
        for (var ii = 0; ii < data[i].length; ii++) {
          export_str += data[i][ii]
          export_str += (ii === data[i].length-1) ? "\r\n" : ","
        }
      } else {
        keys = Object.keys(data[i])
        for (var ii = 0; ii < keys.length; ii++) {
          export_str += keys[ii]
          export_str += ","
          export_str += data[i][keys[ii]]
          export_str += (ii === keys.length-1) ? "\r\n" : ","
        }
      }
    }
    return export_str
  }

  export_csv( export_str, filename, dir_path ) {
    var path = './' dir_path + "/" + filename + ".csv"
    this.fs.writeFile(path, export_str, function(err) {
      if(err) {
        return console.log(err)
      }
      console.log("saved " + path)
    })
  }

}

module.exports = Schema
