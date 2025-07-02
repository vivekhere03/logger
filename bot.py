import discord
from discord.ext import commands
import datetime
import os
import logging
from dotenv import load_dotenv  # Optional: For local testing

load_dotenv()
TOKEN = os.getenv("DISCORD_TOKEN")
if not TOKEN:
    raise ValueError("❌ DISCORD_TOKEN is not set! Make sure to set it in Railway or in your .env file.")

TRACKED_USER_IDS = [711623890438324296, 992021289260830770, 962977955225677864]
LOG_RECEIVER_ID = 1310377765781897266

logging.basicConfig(
    level=logging.INFO,
    format='%(asctime)s - %(name)s - %(levelname)s - %(message)s',
    handlers=[
        logging.FileHandler('bot.log'),
        logging.StreamHandler()
    ]
)
logger = logging.getLogger(__name__)

intents = discord.Intents.default()
intents.members = True
intents.message_content = True
intents.presences = True
intents.voice_states = True

bot = commands.Bot(command_prefix="!ishuu", intents=intents)

async def send_log(log_message):
    logger.info(log_message)
    try:
        log_receiver = await bot.fetch_user(LOG_RECEIVER_ID)
        await log_receiver.send(log_message)
    except discord.errors.Forbidden:
        logger.warning("Could not send DM to the log receiver. They may have DMs disabled.")
    except discord.errors.NotFound:
        logger.error("Log receiver user not found.")
    except Exception as e:
        logger.error(f"Error sending log message: {e}")

@bot.event
async def on_ready():
    logger.info(f'Logged in as {bot.user.name} (ID: {bot.user.id})')
    logger.info(f'Discord.py version: {discord.__version__}')
    logger.info(f'Bot is in {len(bot.guilds)} guilds')
    await send_log(f"🟢 Bot is online and ready! Monitoring {len(TRACKED_USER_IDS)} users.")

@bot.event
async def on_presence_update(before, after):
    if before.id in TRACKED_USER_IDS:
        try:
            if before.status != after.status:
                await send_log(f"[{datetime.datetime.now()}] 📊 {after.name} status: {before.status} → {after.status}")

            before_spotify = next((a for a in before.activities if isinstance(a, discord.Spotify)), None)
            after_spotify = next((a for a in after.activities if isinstance(a, discord.Spotify)), None)
            if before_spotify != after_spotify:
                if after_spotify:
                    msg = f"[{datetime.datetime.now()}] 🎵 {after.name} started '{after_spotify.title}' by {after_spotify.artist}"
                else:
                    msg = f"[{datetime.datetime.now()}] ⏹️ {after.name} stopped Spotify"
                await send_log(msg)
        except Exception as e:
            logger.error(f"Error in presence update: {e}")

@bot.event
async def on_message(message):
    if message.author.id in TRACKED_USER_IDS and not message.author.bot:
        try:
            content = message.content[:200] + "..." if len(message.content) > 200 else message.content
            await send_log(f"[{datetime.datetime.now()}] 💬 {message.author.name} in #{message.channel}: {content}")
        except Exception as e:
            logger.error(f"Error logging message: {e}")
    await bot.process_commands(message)

@bot.event
async def on_message_delete(message):
    if message.author.id in TRACKED_USER_IDS and not message.author.bot:
        try:
            content = message.content[:200] + "..." if len(message.content) > 200 else message.content
            await send_log(f"[{datetime.datetime.now()}] 🗑️ Deleted by {message.author.name} in #{message.channel}: {content}")
        except Exception as e:
            logger.error(f"Error logging deleted message: {e}")

@bot.event
async def on_voice_state_update(member, before, after):
    if member.id in TRACKED_USER_IDS:
        try:
            if before.channel != after.channel:
                action = f"joined '{after.channel.name}'" if after.channel else f"left '{before.channel.name}'"
                await send_log(f"[{datetime.datetime.now()}] 🔊 {member.name} {action}")

            if before.self_mute != after.self_mute:
                status = "muted" if after.self_mute else "unmuted"
                await send_log(f"[{datetime.datetime.now()}] {member.name} {status} themselves")

            if before.self_deaf != after.self_deaf:
                status = "deafened" if after.self_deaf else "undeafened"
                await send_log(f"[{datetime.datetime.now()}] {member.name} {status} themselves")
        except Exception as e:
            logger.error(f"Error in voice state update: {e}")

@bot.command(name="status")
async def bot_status(ctx):
    if ctx.author.id == LOG_RECEIVER_ID:
        embed = discord.Embed(title="Bot Status", color=0x00ff00)
        embed.add_field(name="Status", value="✅ Online", inline=True)
        embed.add_field(name="Tracked Users", value=len(TRACKED_USER_IDS), inline=True)
        embed.add_field(name="Servers", value=len(bot.guilds), inline=True)
        embed.add_field(name="Latency", value=f"{round(bot.latency * 1000)}ms", inline=True)
        await ctx.send(embed=embed)

@bot.command(name="ping")
async def ping(ctx):
    if ctx.author.id == LOG_RECEIVER_ID:
        await ctx.send(f"🏓 Pong! Latency: {round(bot.latency * 1000)}ms")

@bot.event
async def on_error(event, *args, **kwargs):
    logger.error(f"An error occurred in {event}: {args}, {kwargs}")

@bot.event
async def on_command_error(ctx, error):
    if isinstance(error, commands.CommandNotFound):
        return
    logger.error(f"Command error: {error}")

def main():
    try:
        logger.info("Starting Discord bot...")
        bot.run(TOKEN)
    except discord.LoginFailure:
        logger.error("Invalid bot token!")
    except Exception as e:
        logger.error(f"Failed to start bot: {e}")

if __name__ == "__main__":
    main()
