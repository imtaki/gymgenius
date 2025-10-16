import { PrismaClient } from "@prisma/client"; 
import bcrypt from "bcrypt";
import crypto from "crypto";
import jwt from "jsonwebtoken";
const JWT_SECRET = process.env.JWT ?? "secret";
import { sendVerificationCode } from "../utils/email";


const prisma = new PrismaClient();
import { Request, Response } from "express";
export const registerUser = async (req: Request, res: Response): Promise<void> => {
    const { username, password, email } = req.body;

    if (!username || !password || !email) {
        res.status(400).json({ message: 'All fields are required' });
        return;
    }

    try {
        const existingUser = await prisma.user.findFirst({
            where: {
                OR: [
                    { username },
                    { email }
                ]
            }
        });

        if (existingUser) {
            res.status(409).json({ message: 'Username or email already exists' });
        }
;

        const hashedPassword = await bcrypt.hash(password, 10);
        const verificationToken = Math.floor(10000 + Math.random() * 90000).toString();
        const newUser = await prisma.user.create({
            data: {
                username,
                password: hashedPassword,
                email,
                isVerified: false,
                role: 'USER'
            }
        });

        await prisma.verificationToken.create({
            data: {
                token: verificationToken,
                userId: newUser.id,
                expiresAt: new Date(Date.now() + 10 * 60 * 1000)
            }
        });

        await sendVerificationCode(email, verificationToken);


        res.status(201).json({ message: 'User registered successfully. Please check your email to verify your account.' });
        return;
    } catch (error) {
        console.error(error);
        res.status(500).json({ message: 'Internal server error' });
    }
}


export const loginUser= async (req: Request, res: Response): Promise<any> => {
    const { email, password } = req.body;

    try {
        const user = await prisma.user.findUnique({
            where: { email }
        });
3
        if (!user) {
            return res.status(401).json({ message: "Invalid email or password" });
        }

        const isPasswordValid = await bcrypt.compare(password, user.password);
        if (!isPasswordValid) {
            return res.status(401).json({ message: "Invalid email or password" });
        }

        const accessToken = jwt.sign({ id: user?.id, username: user.username, role: user.role }, JWT_SECRET, { expiresIn: "1h" });


        res.status(200).json({
            message: "Login successful",
            accessToken,
            user: {
                id: user?.id,
                username: user?.username,
                role: user.role,
                email: user?.email
            }
        });
    } catch (error: any) {
        console.error("Error during login:", error);
        res.status(500).json({ message: "Internal server error" });
        return;
    }
};


export const logoutUser = async (req: Request, res: Response): Promise<void> => {
    const { token } = req.body;
    res.clearCookie("refreshToken");
    res.status(200).json({ message: "Logout successful" });
}


export const verifyEmail = async (req: Request, res: Response): Promise<void> => {
  const { email, code } = req.body;

  if (!email || !code) {
    res.status(400).json({ message: "Email and code are required" });
    return;
  }

  try {
    const user = await prisma.user.findUnique({ where: { email } });
    if (!user) {
      res.status(404).json({ message: "User not found" });
      return;
    }

    const storedToken = await prisma.verificationToken.findFirst({
      where: { token: code, userId: user.id },
    });

    if (!storedToken) {
      res.status(400).json({ message: "Invalid verification code" });
      return;
    }

    if (storedToken.expiresAt < new Date()) {
      await prisma.verificationToken.delete({ where: { id: storedToken.id } });
      res.status(400).json({ message: "Verification code expired" });
      return;
    }

    await prisma.user.update({
      where: { id: user.id },
      data: { isVerified: true },
    });

    await prisma.verificationToken.delete({ where: { id: storedToken.id } });

    res.status(200).json({ message: "Email verified successfully" });
  } catch (error) {
    console.error("Error verifying email:", error);
    res.status(500).json({ message: "Internal server error" });
  }
};
