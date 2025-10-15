import nodemailer from "nodemailer";

const transport = nodemailer.createTransport({
  host: "sandbox.smtp.mailtrap.io",
  port: 2525,
  auth: {
    user: process.env.EMAIL_USER,
    pass: process.env.EMAIL_PASS,
  },
});


export async function sendVerificationCode(toEmail: string, verificationCode: string) {
    await transport.sendMail({
      from: "domtaki197@gmail.com",
      to: [toEmail],
      subject: "Your Verification Code",
      html: `
      <h3>Your verification code:</h3>
      <h2>${verificationCode}</h2>
      <p>This code will expire in 10 minutes.</p>
    `,
    });
  }