/**
 * @package   mod_pdfannotator
 * @copyright 2018 RWTH Aachen, Friederike Schwager (see README.md)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

// R: The first parameter has to be Y, because it is a default YUI-object (demanded by moodle).
function setCharts(Y, names, otherquestions, myquestions, otheranswers, myanswers) {
    require(['core/chartjs'], function (Chart) {
        // On small screens set width depending on number of annotators. Otherwise the diagram is very small.
        let width = Math.max(names.length * 25, 300);
        width = names.length * 40;
        if (window.innerWidth < width) {
            document.getElementById('chart-container').style.width = width + "px";
        }

        var maxValue = calculateMax(otherquestions, myquestions, otheranswers, myanswers);

        var ctx = document.getElementById('myChart').getContext('2d');
        var myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: names,
                datasets: [{
                        label: M.util.get_string('myquestions', 'pdfannotator'),
                        stack: 'questions',
                        data: myquestions,
                        backgroundColor: 'rgb(0,84,159)',
                    }, {
                        label: M.util.get_string('questions', 'pdfannotator') + ' ' + M.util.get_string('by_other_users', 'pdfannotator'),
                        stack: 'questions',
                        data: otherquestions,
                        backgroundColor: 'rgb(142,186,229)',
                    },
                    {
                        label: M.util.get_string('myanswers', 'pdfannotator'),
                        stack: 'answers',
                        data: myanswers,
                        backgroundColor: 'rgb(87,171,39)',
                    },
                    {
                        label: M.util.get_string('answers', 'pdfannotator') + ' ' + M.util.get_string('by_other_users', 'pdfannotator'),
                        stack: 'answers',
                        data: otheranswers,
                        backgroundColor: 'rgb(184,214,152)',
                    }]
            },
            options: {
                maintainAspectRatio: false,
                title: {
                    display: true,
                    text: M.util.get_string('chart_title', 'pdfannotator'),
                    fontSize: 20
                },
                legend: {
                    display: true,
                    position: 'bottom'
                },
                scales: {

                    xAxes: [{
                            stacked: true
                        }],
                    yAxes: [{
                            ticks: {
                                beginAtZero: true,
                                precision: 0,
                                max: maxValue + 2
                            }
                        }]
                },
                tooltips: {
                    mode: 'x'
                },
                layout: {
                    padding: {
                        left: 0,
                        right: 0,
                        top: 0,
                        bottom: 0
                    }
                }
            }
        });

    });
}

function calculateMax(otherquestions, myquestions, otheranswers, myanswers) {
    let max = 0;
    for (let i = 0; i < otherquestions.length; ++i) {
        if (otherquestions[i] + myquestions[i] > max) {
            max = otherquestions[i] + myquestions[i];
        }
        if (otheranswers[i] + myanswers[i] > max) {
            max = otheranswers[i] + myanswers[i];
        }
    }

    return max;
}
