let car = [
  {
    model: "van1",
    rsvpStartDate: "2022-10-10T00:00:00.000Z",
    rsvpEndDate: "2022-10-12T00:00:00.000Z",
    rsvpDates: "",
  },
  {
    model: "van2",
    rsvpStartDate: "2022-10-12T00:00:00.000Z",
    rsvpEndDate: "2022-10-13T00:00:00.000Z",
  },
  {
    model: "van3",
    rsvpStartDate: "2022-10-11T00:00:00.000Z",
    rsvpEndDate: "2022-10-14T00:00:00.000Z",
  },
];

const userDates = ["2022-10-10", "2022-10-13"];

function getDatesInRange(startDate, endDate) {
  const date = new Date(startDate.getTime());

  const dates = [];

  while (date <= endDate) {
    dates.push(new Date(date));
    date.setDate(date.getDate() + 1);
  }

  return dates;
}

const str = new Date(userDates[0]);
const ed = new Date(userDates[1]);

let userDatesRange = getDatesInRange(str, ed);

// userDatesRange.map((date, i) => {
//   car.map(({ model, rsvpStartDate, rsvpEndDate }) => {
//     date == rsvpStartDate
//       ? console.log("here", model)
//       : console.log("not here");
//   });
// });

console.log(userDatesRange[0]);
console.log(car[0].rsvpStartDate >= userDatesRange[0]);

userDatesRange.map((date) => {
  console.log(date);
  console.log(date == car[0].rsvpStartDate);
});

// console.log(userDatesRange);

// car.map(({ model, rsvpStartDate, rsvpEndDate }, idx) => {
//   console.log(
//     rsvpStartDate == userDatesRange
//       ? "good" + rsvpStartDate
//       : "bad" + rsvpStartDate
//   );
// });
//map
// car.map(
//   ({ model, rsvpStartDate, rsvpEndDate }) => {
//     userDatesRange.indexOf(userDates[0]) === rsvpStartDate
//       ? console.log(model)
//       : "badrequest";
//   }
// );
