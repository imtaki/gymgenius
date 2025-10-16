import express from "express";
import { registerUser, loginUser, logoutUser, verifyEmail,} from "../Controllers/AuthController";

const router = express.Router();

router.post('/register', registerUser);
router.post('/login', loginUser);
router.post('/logout', logoutUser)
router.post('/verify-email', verifyEmail);

export default router;