export function getUTCDateString(dateObj) {
  const year = dateObj.getUTCFullYear();
  const month = String(dateObj.getUTCMonth() + 1).padStart(2, '0'); // Month is 0-indexed
  const day = String(dateObj.getUTCDate()).padStart(2, '0');
  return `${year}-${month}-${day}`;
}

export function getUTCTimeString(dateObj) {
  const hours = String(dateObj.getUTCHours()).padStart(2, '0');
  const minutes = String(dateObj.getUTCMinutes()).padStart(2, '0');
  return `${hours}:${minutes}`;
}

export function convertUtcToCet(utcDate) {
  // Create a new Date object from the UTC date
  const date = new Date(utcDate);

  // Get the UTC time in milliseconds
  const utcTime = date.getTime();

  // Calculate the CEST offset in milliseconds (2 hours * 60 minutes * 60 seconds * 1000 milliseconds)
  const cestOffsetMilliseconds = 2 * 60 * 60 * 1000;

  // Add the CEST offset to the UTC time
  const cetTime = utcTime + cestOffsetMilliseconds;

  // Create a new Date object with the CET time (which will be interpreted in the local time zone of the user's system)
  const cetDate = new Date(cetTime);

  return cetDate;
}