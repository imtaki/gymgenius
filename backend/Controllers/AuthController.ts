import { PrismaClient } from "@prisma/client"; 
import bcrypt from "bcrypt";
import crypto from "crypto";

const prisma = new PrismaClient();
import { Request, Response } from "express";
export const registerUser = async (req: Request, res: Response): Promise<void> => {
    const { userName, password, email } = req.body;

    if (!userName || !password || !email) {
        res.status(400).json({ message: 'All fields are required' });
    }

    try {
        const existingUser = await prisma.user.findFirst({
            where: {
                OR: [
                    { userName },
                    { email }
                ]
            }
        });

        if (existingUser) {
            res.status(409).json({ message: 'Username or email already exists' });
        }
        
        const hashedPassword = await bcrypt.hash(password, 10);
        //const verificationToken = crypto.randomBytes(32).toString('hex');
        const newUser = await prisma.user.create({
            data: {
                userName,
                password: hashedPassword,
                email,
                isVerified: false,
                role: 'USER'
                //verificationToken
            }
        });

        // Send verification email logic here (omitted for brevity)

        res.status(201).json({ message: 'User registered successfully. Please check your email to verify your account.' });
        return;
    } catch (error) {
        console.error(error);
        res.status(500).json({ message: 'Internal server error' });
    }
}