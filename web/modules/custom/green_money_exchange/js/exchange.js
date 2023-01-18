// (
//   function ($, Drupal, drupalSettings) {
//     'use strict'
//
//     debugger;
//     Drupal.behavior.money = {
//       attach: function (context, settings) {
//
//         alert("aaaaaaaaa");
//
//         const ctx = document.getElementById('greenChart');
//
//         console.log(ctx);
//
//         new Chart(ctx, {
//           type: 'bar',
//           data: {
//             labels: ['Red', 'Blue', 'Yellow', 'Green', 'Purple', 'Orange'],
//             datasets: [{
//               label: '# of Votes',
//               data: [12, 19, 3, 5, 2, 3],
//               borderWidth: 1
//             }]
//           },
//           options: {
//             scales: {
//               y: {
//                 beginAtZero: true
//               }
//             }
//           }
//         });
//       }
//     };
//   }
// )(jQuery, Drupal, drupalSettings);

const ctx = document.getElementById('greenChart');

console.log(ctx);

new Chart(ctx, {
  type: 'bar',
  data: {
    labels: ['Red', 'Blue', 'Yellow', 'Green', 'Purple', 'Orange'],
    datasets: [{
      label: '# of Votes',
      data: [12, 19, 3, 5, 2, 3],
      borderWidth: 1
    }]
  },
  options: {
    scales: {
      y: {
        beginAtZero: true
      }
    }
  }
});
