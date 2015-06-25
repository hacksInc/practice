//!function( $ ) {
var ViewKpi = (function () {
    var ViewKpi = function(kpi_data, page) {
    // var ViewKpi = function () {
//        var data = kpi_data,
//            hoge = '';
        this.nowPage = page;
        this.maxPage = 1;
        this.pageMaxDay = 10;
        this.searchDateFrom = now_date;
        this.searchOsType = 'all';
    };
    ViewKpi.prototype = {
        constructor: ViewKpi,
        nextView: function () {
            var page_no = parseInt(this.nowPage);
            this.nowPage = page_no + 1;
        },
        prevView: function (page) {
            var page_no = parseInt(this.nowPage);
            this.nowPage = page_no - 1;
        },
        _viewDisplayData: function () {

        }
    };
    return ViewKpi;
//}( window.jQuery );
})();
/**
 *
 *
 */
var KpiDate = (function () {
    //var KpiDate = function (input_date_from,input_date_to) {
    var KpiDate = function (input_date_from) {
        this.view_date_from=input_date_from;
//        this.view_date_to=input_date_to;
    };
    KpiDate.prototype = {
        setDate: function ( input_date ) {
            this.view_date_from = input_date;
        },
        getDate: function () {
            return this.view_date_from;
//            var tmp_date = this.date;
//            return tmp_date.replase(/\//g, "-");
        },
        prevMonth: function () {
            var date_obj = new Date(this.view_date_from);
            var year = date_obj.getFullYear(),
                month = date_obj.getMonth() + 1,
                day = date_obj.getDate(),
                month_last_date = '',
                p_year = year,
                p_month = month - 1,
                p_day = day,
                p_month_last_date = '',
                a_year = date_obj.getFullyear;

            // 年またぎチェック
            if (p_month == 0){
                p_month = 12;
                p_year = parseInt(year) - 1;
            }

            // 月末日チェック
            p_month_last_date = this.getMonthLastDay(p_year, p_month);
            if (day > p_month_last_date) {
                p_day = p_month_last_date;
            }

            this.view_date_from = this._editDate(p_year, p_month, p_day);
        },
        nextMonth: function () {
            var date_obj = new Date(this.view_date_from);
            var year = date_obj.getFullYear(),
                month = date_obj.getMonth() + 1,
                day = date_obj.getDate(),
                month_last_date = '',
                n_year = year,
                n_month = month + 1,
                n_day = day,
                n_month_last_date = '',
                a_year = date_obj.getFullyear;

            // 年またぎチェック
            if (n_month == 13){
                n_month = 1;
                n_year = parseInt(year) + 1;
            }

            // 月末日チェック
            n_month_last_date = this.getMonthLastDay(n_year, n_month);
            if (day > n_month_last_date) {
                n_day = n_month_last_date;
            }

            this.view_date_from = this._editDate(n_year, n_month, n_day);
        },
        prevDay: function () {
            var date_obj = new Date(this.view_date_from);
            var year = date_obj.getFullYear(),
                month = date_obj.getMonth() + 1,
                day = date_obj.getDate(),
                month_last_date = '',
                p_year = year,
                p_month = month,
                p_day = day - 1,
                p_month_last_date = '',
                a_year = date_obj.getFullyear;

            // 月またぎチェック
            if (p_day == 0){
                p_month = p_month - 1;
            }

            // 年またぎチェック
            if (p_month == 0){
                p_month = 12;
                p_year = parseInt(year) - 1;
            }

            // 月末日チェック
            p_month_last_date = this.getMonthLastDay(p_year, p_month);
            if (p_day == 0) {
                p_day = p_month_last_date;
            }

            this.view_date_from = this._editDate(p_year, p_month, p_day);
        },
        nextDay: function () {
            var date_obj = new Date(this.view_date_from);
            var year = date_obj.getFullYear(),
                month = date_obj.getMonth() + 1,
                day = date_obj.getDate(),
                month_last_date = '',
                n_year = year,
                n_month = month,
                n_day = day + 1,
                n_month_last_date = '',
                a_year = date_obj.getFullyear;

            // 月末日チェック
            n_month_last_date = this.getMonthLastDay(n_year, n_month);
            if (n_day > n_month_last_date) {
                n_day = 1;
                n_month = n_month + 1;
            }

            // 年またぎチェック
            if (n_month == 13){
                n_month = 1;
                n_year = parseInt(year) + 1;
            }
            this.view_date_from = this._editDate(n_year, n_month, n_day);
        },
        getMonthLastDay: function (year, month) {
            var date_obj = new Date(year, month, 0);
            return date_obj.getDate();
            
        },
        parseDate: function (){
            var tmp_date = this.date;
            return tmp_date.replase(/-/g, "/");
        },
        checkDate: function (){
            var date_obj = new Date(this.date);
            if (isNaN(date_obj)){
                return false;
            }
            var year = date_obj.getFullyear(),
                month = date_obj.getMonth(),
                date = date_obj.getDate(),
                split_date = this.view_date.split("-");
                month_last_date = '',
                hoge = '';
            if ( year == parseInt(split_date[0]) && month == parseInt(split_date[1]) && date == parseInt(split_date[2]) ){
                return false;
            }
            return true;
        },
        /**
         * 日付のyyyy/mm/dd形式編集
         */
        _editDate: function (year, month, day) {
            var display_date = '',
                disp_month = month,
                disp_day = day;

            if (month < 10) {
                disp_month = "0" + month;
            }
            if (day < 10) {
                disp_day = "0" + day;
            }
            display_date = year + "-" + disp_month + "-" + disp_day;
            return display_date;
        }

    };
    return KpiDate;
})();
