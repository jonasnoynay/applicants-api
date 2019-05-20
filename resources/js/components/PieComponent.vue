<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card mt-4">
                    <div class="card-header">PIE CHART</div>

                    <div class="card-body">
                        <h2 class="text-center">{{ filters[filterSelected] }}</h2>
                       <canvas ref="myChart" width="400" height="400"></canvas>
                       <div class="container mt-4">
                            <div class="row">
                            <button class="btn p-2 flex-fill m-2" v-for="(filter, index) in filters" :class="{ 'btn-primary': index == filterSelected, 'btn-secondary' : index != filterSelected }" @click="changeFilter(index)">{{ filter }}</button>
                        </div>
                       </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    let Chart = require('chart.js');
    let uri = 'http://applicants-api.test/api/v1';
    let pie = 'null';

    var config = {
        type: 'pie',
        data: {
            datasets: [{
                data: [],
                backgroundColor: [
                    '#CCCCCC',
                    '#ADD9E6',
                ],
            }],
            labels: [
                'Not Hired',
                'Hired',
            ]
        },
        options: {
            responsive: true
        }
    };
    
    export default {
        data() {
            return {
                applicants: [],
                filters: {
                    'last-week' : 'Last Week',
                    'this-week' : 'This Week',
                    'this-month' : 'This Month',
                },
                filterSelected: 'this-month'
            }
        },

        computed: {
            dataset: function() {
              
                let start;
                let end;

                if(this.filterSelected == 'last-week') {
                    start = moment().subtract(1, 'weeks').startOf('isoWeek');
                    end = moment().subtract(1, 'weeks').endOf('isoWeek');
                } else if(this.filterSelected == 'this-week') {
                    start = moment().startOf('isoWeek');
                    end = moment().endOf('isoWeek');
                } else if(this.filterSelected == 'this-month') {
                    start = moment().startOf('month');
                    end = moment().endOf('month');
                }

                let filtered = _.filter(this.applicants, function(a) {
                    let created_at = moment(a.created_at);

                    return created_at >= start && created_at <= end;
                });

                let grouped = _.groupBy(filtered, function(a) {
                    return a.isHired;
                });

                return [grouped[0] ? grouped[0].length : 0, grouped[1] ? grouped[1].length : 0];
            }
        },

        methods : {
            updatePie() {
                pie.data.datasets.forEach((dataset) => {
                    dataset.data = this.dataset;
                });

                pie.update();
            },
            changeFilter(selected) {
                this.filterSelected = selected;
                this.updatePie();
            }
        },

        mounted() {
            pie = new Chart(this.$refs.myChart.getContext('2d'), config);
        },

        created() {
            axios.get(uri+"/applicants").then(response => {
                this.applicants = response.data.data;
                this.updatePie();
            });
        }
    }
</script>
