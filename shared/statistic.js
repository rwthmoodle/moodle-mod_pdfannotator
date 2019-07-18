/**
 * @package   mod_pdfannotator
 * @copyright 2018 RWTH Aachen, Friederike Schwager (see README.md)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

// R: The first parameter has to be Y, because it is a default YUI-object (demanded by moodle).
function setCharts(Y, names, otherquestions, myquestions, otheranswers, myanswers) {
    Highcharts.setOptions({
        colors: ['rgb(0,84,159)', 'rgb(142,186,229)', 'rgb(87,171,39)', 'rgb(184,214,152)']
    });

    Highcharts.chart('chart_questions_answers', {
        chart: {
            type: 'column',
            borderColor: 'black',
            borderWidth: 1
        },
        title: {
            text: M.util.get_string('chart_title', 'pdfannotator'),
        },
        xAxis: {
            categories: names
        },
        yAxis: {
            allowDecimals: false,
            title: {
                text: M.util.get_string('count', 'pdfannotator')
            },
            reversedStacks: false
        },
        plotOptions: {
            column: {
                stacking: 'normal'
            }
        },
        tooltip: {
            headerFormat: '<b>{point.key}</b><br/>',
            pointFormat: '{series.name}: {point.y}<br/>' + M.util.get_string('total', 'pdfannotator') + ': {point.stackTotal}'
        },
        legend: {
            itemWidth: 225
        },
        series: [{
            name: M.util.get_string('myquestions', 'pdfannotator'),
            data: myquestions,
            stack: 'questions'
        }, {
            name: M.util.get_string('questions', 'pdfannotator') + ' ' + M.util.get_string('by_other_users', 'pdfannotator'),
            data: otherquestions,
            stack: 'questions'
        }, {
            name: M.util.get_string('myanswers', 'pdfannotator'),
            data: myanswers,
            stack: 'answers'
        }, {
            name: M.util.get_string('answers', 'pdfannotator') + ' ' + M.util.get_string('by_other_users', 'pdfannotator'),
            data: otheranswers,
            stack: 'answers'
        }]
    });

}
