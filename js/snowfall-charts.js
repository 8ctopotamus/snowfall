const { all_snowfall_data } = wp_data

const canvas1 = document.getElementById('most-snow-1-day');
canvas1.height = (all_snowfall_data.length * 10);
ctx1 = canvas1.getContext('2d');

const cityNames = all_snowfall_data.map(city => city['Name']);
const OneDayDates = all_snowfall_data.map(city => city['1 day DATE']);
const OneDayQTY = all_snowfall_data.map(city => city['1 day QTY']);

const mostSnow1DayChart = new Chart(ctx1, {
  type: 'horizontalBar',
  data: {
    labels: cityNames,
    datasets: [{
      label: 'Inches',
      data: OneDayQTY,
    }]
  },
});